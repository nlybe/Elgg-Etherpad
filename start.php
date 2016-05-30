<?php
/**
 * Elgg Etherpad lite plugin
 *
 * @package etherpad
 * @override mod/pages/start.php
 * 
 * nikos: http://www.emiprotechnologies.com/technical_notes/odoo-technical-notes-59/post/install-configure-and-setting-up-etherpad-server-for-odoo-253
 * 
sudo su - etherpad -s /bin/bash 
mkdir -p ~/local/etherpad
cd ~/local/etherpad
git clone git://github.com/ether/etherpad-lite.git

cd etherpad-lite
bin/run.sh
* 
to run:
* sudo su - etherpad -s /bin/bash
* ./bin/run.sh
*/
 
elgg_register_event_handler('init', 'system', 'etherpad_init');

/**
 * Initialize the etherpad plugin.
 *
 */
function etherpad_init() {

    // override pages library
	elgg_register_library('elgg:pages', elgg_get_plugins_path() . 'etherpad/lib/pages.php');

	$page_integration = elgg_get_plugin_setting('integrate_in_pages', 'etherpad') != 'yes';

	if ($page_integration) {
		$item = new ElggMenuItem('etherpad', elgg_echo('etherpad'), 'etherpad/all');
		elgg_register_menu_item('site', $item);
		
		// Register a url handler
		elgg_register_plugin_hook_handler('entity:url', 'object', 'etherpad_set_url');
	}
	else {
		elgg_register_plugin_hook_handler('entity:url', 'object', 'etherpad_pages_set_url');
	}
	
	// Register a URL handler, so we can have nice URLs
	elgg_register_page_handler('pages', 'etherpad_page_handler');
	elgg_register_page_handler('etherpad', 'etherpad_page_handler');
	elgg_register_plugin_hook_handler('forward', 'etherpad/server_failure', 'elgg_error_page_handler');

	// Register some actions
	$action_base = elgg_get_plugins_path() . 'etherpad/actions';
	elgg_register_action("etherpad/save", "$action_base/etherpad/save.php");
	elgg_register_action("etherpad/delete", "$action_base/etherpad/delete.php");

	// Register entity type for search
	elgg_register_entity_type('object', 'etherpad', 'ElggPad');
	elgg_register_entity_type('object', 'subpad', 'ElggPad');

	if($page_integration) {
		// add to groups
		add_group_tool_option('etherpad', elgg_echo('groups:enablepads'), true);
		elgg_extend_view('groups/tool_latest', 'etherpad/group_module');
	}

	// add a widget
	elgg_register_widget_type('etherpad', elgg_echo('etherpad'), elgg_echo('etherpad:profile:widgetdesc'));
	

	// Language short codes must be of the form "etherpad:key"
	// where key is the array key below
	elgg_set_config('etherpad', array(
		'title' => 'text',
		'tags' => 'tags',
		'parent_guid' => 'parent',
		'access_id' => 'access',
		'write_access_id' => 'write_access',
	));

	if ($page_integration) {
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'etherpad_owner_block_menu');
	}

	// write permission plugin hooks
	elgg_register_plugin_hook_handler('permissions_check', 'object', 'etherpad_write_permission_check');
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'etherpad_container_permission_check');
	
	// icon url override
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'etherpad_icon_url_override');

	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'etherpad_entity_menu_setup');

	elgg_register_event_handler('upgrade', 'system', 'etherpad_run_upgrades');
}

/**
 * Dispatcher for pages.
 * URLs take the form of
 *  All pages:        (pages|etherpad)/all
 *  User's pages:     (pages|etherpad)/owner/<username>
 *  Friends' pages:   (pages|etherpad)/friends/<username>
 *  View page:        (pages|etherpad)/view/<guid>/<title>
 *  New page:         (pages|etherpad)/add/<guid> (container: user, group, parent)
 *  Edit page:        (pages|etherpad)/edit/<guid>
 *  History of page:  (pages|etherpad)/history/<guid>
 *  Revision of page: (pages|etherpad)/revision/<id>
 *  Group pages:      (pages|etherpad)/group/<guid>/all
 *
 * Title is ignored
 *
 * @param array $page
 * @return bool
 */
function etherpad_page_handler($page, $handler) {
	
	elgg_load_library('elgg:pages');

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo($handler), "$handler/all");

	$base_dir = elgg_get_plugins_path() . "etherpad/pages/$handler";

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			include "$base_dir/owner.php";
			break;
		case 'friends':
			include "$base_dir/friends.php";
			break;
		case 'view':
			set_input('guid', $page[1]);
			include "$base_dir/view.php";
			break;
		case 'add':
			set_input('guid', $page[1]);
			include "$base_dir/new.php";
			break;
		case 'edit':
			set_input('guid', $page[1]);
			include "$base_dir/edit.php";
			break;
		case 'group':
			include "$base_dir/owner.php";
			break;
		case 'history':
			set_input('guid', $page[1]);
			include "$base_dir/history.php";
			break;
		case 'revision':
			set_input('id', $page[1]);
			include "$base_dir/revision.php";
			break;
		case 'all':
			include "$base_dir/world.php";
			break;
		default:
			return false;
	}
	return true;
}

/**
 * Format and return the URL for etherpad
 *
 * @param string $hook
 * @param string $type
 * @param string $url
 * @param array  $params
 * @return string URL of etherpad.
 */
function etherpad_set_url($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'etherpad') || elgg_instanceof($entity, 'object', 'subpad')) {
		$friendly_title = elgg_get_friendly_title($entity->title);
		return "etherpad/view/{$entity->guid}/$friendly_title";
	}
}

/**
 * Override the page url
 * 
 * @param string $hook
 * @param string $type
 * @param string $url
 * @param array  $params
 * @return string
 */
function etherpad_pages_set_url($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'etherpad') || elgg_instanceof($entity, 'object', 'subpad')) {
		$friendly_title = elgg_get_friendly_title($entity->title);
		return "pages/view/{$entity->guid}/$title";
	}
}

/**
 * Override the default entity icon for pads
 *
 * @return string Relative URL
 */
function etherpad_icon_url_override($hook, $type, $returnvalue, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'etherpad') ||
		elgg_instanceof($entity, 'object', 'subpad')) {
		switch ($params['size']) {
			case 'small':
				return 'mod/etherpad/images/etherpad.png';
				break;
			case 'medium':
				return 'mod/etherpad/images/etherpad_lrg.png';
				break;
		}
	}
}

/**
 * Add a menu item to the user ownerblock
 */
function etherpad_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "etherpad/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('etherpad', elgg_echo('etherpad'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->pages_enable != "no") {
			$url = "etherpad/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('etherpad', elgg_echo('etherpad:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

/**
 * Add links/info to entity menu particular to pages plugin
 */
function etherpad_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'etherpad') {
		return $return;
	}

	// remove delete if not owner or admin
	// @todo Elgg 1.9 has a function to remove a menu item.
	if (!elgg_is_admin_logged_in() && elgg_get_logged_in_user_guid() != $entity->getOwnerGuid()) {
		foreach ($return as $index => $item) {
			if ($item->getName() == 'delete') {
				unset($return[$index]);
			}
		}
	}

	// timeslider button, show only if pages integration is enabled.
	$handler = elgg_get_plugin_setting('integrate_in_pages', 'etherpad') == 'yes' ? 'pages' : 'etherpad';
	if($handler == 'pages') {
		$options = array(
			'name' => 'etherpad-timeslider',
			'text' => elgg_echo('etherpad:timeslider'),
			'href' => "$handler/history/$entity->guid",
			'priority' => 200,
		);
		$return[] = ElggMenuItem::factory($options);
	}

    if (elgg_in_context('full_view')) {
    	// fullscreen button
    	$entity = new ElggPad($entity->guid);
    	$options = array(
    		'name' => 'etherpadfs',
    		'text' => elgg_echo('etherpad:fullscreen'),
    		'href' => $entity->getPadPath(),
    		'priority' => 200,
    	);
    	$return[] = ElggMenuItem::factory($options);
    }

	return $return;
}

/**
* Returns a more meaningful message
*
* @param unknown_type $hook
* @param unknown_type $entity_type
* @param unknown_type $returnvalue
* @param unknown_type $params
*/
function etherpad_notify_message($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];

	if (elgg_instanceof($entity, 'object', 'etherpad') || elgg_instanceof($entity, 'object', 'subpad')) {
		$descr = $entity->description;
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		
		return elgg_echo('pages:notification', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}

/**
 * Extend permissions checking to extend can-edit for write users.
 *
 * @param string $hook
 * @param string $entity_type
 * @param bool   $returnvalue
 * @param array  $params
 */
function etherpad_write_permission_check($hook, $entity_type, $returnvalue, $params)
{
	if ($params['entity']->getSubtype() == 'etherpad'
		|| $params['entity']->getSubtype() == 'subpad') {

		$write_permission = $params['entity']->write_access_id;
		$user = $params['user'];

		if ($write_permission && $user) {
			switch ($write_permission) {
				case ACCESS_PRIVATE:
					// Elgg's default decision is what we want
					return;
					break;
				case ACCESS_FRIENDS:
					$owner = $params['entity']->getOwnerEntity();
					if ($owner && $owner->isFriendsWith($user->guid)) {
						return true;
					}
					break;
				default:
					$list = get_access_array($user->guid);
					if (in_array($write_permission, $list)) {
						// user in the access collection
						return true;
					}
					break;
			}
		}
	}
}

/**
 * Extend container permissions checking to extend can_write_to_container for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function etherpad_container_permission_check($hook, $entity_type, $returnvalue, $params) {

	if (elgg_get_context() == "etherpad") {
		if (elgg_get_page_owner_guid()) {
			if (can_write_to_container(elgg_get_logged_in_user_guid(), elgg_get_page_owner_guid())) return true;
		}
		if ($page_guid = get_input('page_guid',0)) {
			$entity = get_entity($page_guid);
		} else if ($parent_guid = get_input('parent_guid',0)) {
			$entity = get_entity($parent_guid);
		}
		if ($entity instanceof ElggObject) {
			if (
					can_write_to_container(elgg_get_logged_in_user_guid(), $entity->container_guid)
					|| in_array($entity->write_access_id,get_access_list())
				) {
					return true;
			}
		}
	}

}

function etherpad_run_upgrades() {
	if (include_once(elgg_get_plugins_path() . 'upgrade-tools/lib/upgrade_tools.php')) {
		upgrade_module_run('etherpad');
	}
}
