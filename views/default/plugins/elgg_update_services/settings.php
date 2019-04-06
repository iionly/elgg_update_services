<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

// Notify by mail
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('elgg_update_services:notify_mail_address'),
	'name' => 'params[notify_mail_address]',
	'value' => $plugin->notify_mail_address,
]);
