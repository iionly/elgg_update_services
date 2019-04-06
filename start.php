<?php
/*******************************************************************************
 * elgg_update_services
 *
 * @author RayJ
 ******************************************************************************/
 
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/hooks.php');

elgg_register_event_handler('init', 'system', 'elgg_update_services_init');

function elgg_update_services_init() {
	elgg_register_plugin_hook_handler('cron', 'hourly', 'elgg_update_services_cron');
	elgg_register_admin_menu_item('administer', 'manageupdate', 'administer_utilities');
}
