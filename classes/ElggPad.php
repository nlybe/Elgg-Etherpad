<?php
/**
 * Elgg EtherPad
 *
 *
 */
class ElggPad extends ElggObject {
	
	protected $pad;
	
	/**
	 * Initialise the attributes array to include the type,
	 * title, and description.
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = "etherpad";
	}
	
	function save(){
		try {
			$sessionID = $this->startSession();
			// Create a pad if not exists
			if (!$this->pname) {
				$name = uniqid();
				$site_mask = str_replace(array('https://', 'http://'), '@', elgg_get_site_url());
				$groupID = $this->get_pad_client()->createGroupIfNotExistsFor($this->container_guid . $site_mask)->groupID;
				$this->get_pad_client()->createGroupPad($groupID, $name, elgg_get_plugin_setting('new_pad_text', 'etherpad'));
				$this->setMetaData('pname', $groupID . "$" . $name);
			}
			
			$padID = $this->getMetadata('pname');
			
			//set etherpad permissions
			if($this->access_id == ACCESS_PUBLIC) {
				$this->get_pad_client()->setPublicStatus($padID, "true");
			} else {
				$this->get_pad_client()->setPublicStatus($padID, "false");
			}
		} catch (Exception $e){
			error_log($e);
			return false;
		}
		$guid = parent::save();
		return $guid;
	}

	function delete(){
		try {
			$this->startSession();
			$this->get_pad_client()->deletePad($this->getMetaData('pname'));
		} catch(Exception $e) {
			error_log('Pad could not be deleted: ' . $this->getMetadata('pname'));
		}
		return parent::delete();
	}
	
	function get_pad_client(){
		if($this->pad){
			return $this->pad;
		}
		
		require_once(elgg_get_plugins_path() . 'etherpad/vendors/etherpad-lite-client.php');

		// Etherpad: Create an instance
		$apikey = elgg_get_plugin_setting('etherpad_key', 'etherpad');
		$apiurl = elgg_get_plugin_setting('etherpad_host', 'etherpad') . "/api";
		$this->pad = new EtherpadLiteClient($apikey, $apiurl);
		return $this->pad;
	}
	
	function startSession() {

		if (!isset($_SESSION['pad_sessions'])) {
			$_SESSION['pad_sessions'] = array();
		}

		if (!isset($this->container_guid)) {
			throw new Exception();
		}

		$user = elgg_get_logged_in_user_entity();
		$container = get_entity($this->container_guid);

		if (in_array("{$user->guid}-{$container->guid}", $_SESSION['pad_sessions'])) {
			return $_SESSION['pad_sessions']["{$user->guid}-{$container->guid}"];
		}

		$site_mask = str_replace(array('https://', 'http://'), '@', elgg_get_site_url());

		//Etherpad: Create an etherpad group for the elgg container
		$groupID = $this->get_pad_client()->createGroupIfNotExistsFor($container->guid . $site_mask)->groupID;

		//Etherpad: Create an author(etherpad user) for logged in user
		$authorID = $this->get_pad_client()->createAuthorIfNotExistsFor($user->username . $site_mask, null)->authorID;

		//Etherpad: Create new session
		$validUntil = mktime(date("H"), date("i") + 5, 0, date("m"), date("d"), date("y")); // 5 minutes in the future
		$sessionID = $this->get_pad_client()->createSession($groupID, $authorID, $validUntil)->sessionID;

		$_SESSION['pad_sessions']["{$user->guid}-{$container->guid}"] = $sessionID;
		$sessionIDs = implode(',', $_SESSION['pad_sessions']);

		$domain = "." . parse_url(elgg_get_site_url(), PHP_URL_HOST);

		if(!setcookie('sessionID', $sessionIDs, $validUntil, '/', $domain)){
			throw new Exception(elgg_echo('etherpad:error:cookies_required'));
		}

		return $sessionID;
	}
	
	protected function getAddress(){
		return elgg_get_plugin_setting('etherpad_host', 'etherpad') . "/p/". $this->getMetadata('pname');
	}
	
	protected function getTimesliderAddress(){
		return $this->getAddress() . "/timeslider";
	}
	
	protected function getReadOnlyAddress(){
		if($this->getMetadata('readOnlyID')){
			$readonly = $this->getMetadata('readOnlyID');
		} else {
			$padID = $this->getMetadata('pname');
			$readonly = $this->get_pad_client()->getReadOnlyID($padID)->readOnlyID;
			$this->setMetaData('readOnlyID', $readonly);
		}
		return elgg_get_plugin_setting('etherpad_host', 'etherpad') . "/ro/". $readonly;
	}
	
	function getPadPath($timeslider = false){
		$settings = array('show_controls', 'monospace_font', 'show_chat', 'line_numbers');
		
		if(elgg_is_logged_in()) {
			$name = elgg_get_logged_in_user_entity()->name;
		} else {
			$name = 'undefined';
		}
		
		array_walk($settings, function(&$setting) {
			if(elgg_get_plugin_setting($setting, 'etherpad') == 'no') {
				$setting = 'false';
			} else {
				$setting = 'true';
			}
		});
		
		$options = '?' . http_build_query(array(
			'userName' => $name,
			'showControls' => $settings[0],
			'useMonospaceFont' => $settings[1],
			'showChat' => $settings[2],
			'showLineNumbers' => $settings[3],
		));

        try {
            $this->startSession();
        } catch(Exception $e) {
            error_log($e->getMessage());
            return false;
        }
		if($this->canEdit() && !$timeslider) {
			return $this->getAddress() . $options;
		} elseif ($this->canEdit() && $timeslider) {
			return $this->getTimesliderAddress() . $options;
		} else {
			return $this->getReadOnlyAddress() . $options;
		}
	}
}
