<?php

$elgg_update_services_update_detail = elgg_extract('elgg_update_services_update_detail', $vars, null);

if (!$elgg_update_services_update_detail) {
	return;
}

echo elgg_format_element('a', ['href' => $elgg_update_services_update_detail["plugin_url"]], $elgg_update_services_update_detail["plugin_name"]);
echo "&nbsp;-&nbsp;" . elgg_echo('elgg_update_services:version') . "&nbsp;" . $elgg_update_services_update_detail["plugin_version"] . "<br>";
echo elgg_format_element('a', ['href' => $elgg_update_services_update_detail["download_url"]], elgg_echo('elgg_update_services:direct_download')). "<br>";
