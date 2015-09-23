<?php
/*******************************************************************************
 * elgg_update_services
 *
 * @author RayJ
 ******************************************************************************/

elgg_register_event_handler('init', 'system', 'elgg_update_services_init');

function elgg_update_services_init() {
	elgg_register_plugin_hook_handler('cron', 'hourly', 'elgg_update_services_cron');
	elgg_register_admin_menu_item('administer', 'manageupdate', 'administer_utilities');
}

function elgg_update_services_get_updates() {
	$installed_plugins = elgg_get_plugins('all');

	$plugin_hash_list = array();
	foreach ($installed_plugins as $id => $plugin) {
		$manifest = $plugin->getManifest();
		$bundled = in_array('bundled', $manifest->getCategories()) ? true : false;
		if (!$bundled) {
			$id = $manifest->getID();
			if (empty($id)) {
				$id = $plugin->getID();
			}
			$version = $manifest->getVersion();
			$author = $manifest->getAuthor();
			$plugin_hash_list[] = md5($id . $version . $author);
		}
	}

	$url = "http://community.elgg.org/services/api/rest/json/?method=plugins.update.check&version=" . elgg_get_version(true);

	foreach ($plugin_hash_list as $plugin_hash) {
		$url .= "&plugins[]=" . $plugin_hash;
	}

	$update_check = elgg_update_services_file_get_conditional_contents($url);

	return json_decode($update_check, true);
}

function elgg_update_services_cron($hook, $entity_type, $returnvalue, $params) {
	// Retrieve the next execution date
	$execution_date = elgg_get_plugin_setting('execution_date', 'elgg_update_services');

	if ($execution_date) {
		if ($execution_date <= time()) {
			// Run the task
			elgg_update_services_check_update();

			// Set the next execution date
			$hour = rand(1, 24);
			$minute = rand(1, 60);
			$execution_date = time() + 604800 + ($hour * $minute * 60); // One week plus random hour and minute (= maximum of 8 days between automatic checks)
			elgg_set_plugin_setting('execution_date', $execution_date, 'elgg_update_services');
		}
	} else {
		elgg_set_plugin_setting('execution_date', time(), 'elgg_update_services');
	}
}

function elgg_update_services_check_update() {
	$update_result = elgg_update_services_get_updates();

	$message = sprintf(elgg_echo('elgg_update_services:message') . "\r\n\r\n");

	if (count($update_result["result"]) > 0) {
		foreach($update_result["result"] as $result) {
			//Compose the e-mail
			$message .= elgg_echo('elgg_update_services:mail_plugin_name') . $result["plugin_name"] . "\r\n";
			$message .= elgg_echo('elgg_update_services:mail_plugin_version') . $result["plugin_version"] . "\r\n";
			$message .= elgg_echo('elgg_update_services:mail_plugin_url') . $result["plugin_url"] . "\r\n";
			$message .= elgg_echo('elgg_update_services:mail_download_url') . $result["download_url"] . "\r\n\r\n";
		}
		// Send the e-mail
		elgg_update_services_notify_admin($message);
	}
}

function elgg_update_services_notify_admin($message){
	$site = elgg_get_site_entity();

	if (($site) && (isset($site->email))) {
		$mailfrom = $site->email;
	} else {
		$mailfrom = 'noreply@' . $site->getDomain();
	}

	$mailto = elgg_get_plugin_setting("notify_mail_address", 'elgg_update_services');

	if($mailto) {
		elgg_send_email($mailfrom, $mailto, elgg_echo('elgg_update_services:subject'), $message);
	}
}

function elgg_update_services_file_get_conditional_contents($szURL) {
	$pCurl = curl_init($szURL);

	curl_setopt($pCurl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($pCurl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($pCurl, CURLOPT_TIMEOUT, 10);

	$szContents = curl_exec($pCurl);
	$aInfo = curl_getinfo($pCurl);

	if ($aInfo['http_code'] === 200) {
		return $szContents;
	}

	return false;
}
