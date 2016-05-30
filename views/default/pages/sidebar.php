<?php
/**
 * Pages sidebar
 * 
 * @override mod/pages/views/default/pages/sidebar.php
 */

echo elgg_view('page/elements/comments_block', array(
	'subtypes' => array('page', 'page_top', 'etherpad', 'subpad'),
	'owner_guid' => elgg_get_page_owner_guid(),
));

echo elgg_view('page/elements/tagcloud_block', array(
	'subtypes' => array('page', 'page_top', 'etherpad', 'subpad'),
	'owner_guid' => elgg_get_page_owner_guid(),
));
