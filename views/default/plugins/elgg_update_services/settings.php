<?php

// Notify by mail
echo "<label>" . elgg_echo('elgg_update_services:notify_mail_address') . "</label>";
echo elgg_view('input/text', array('name' => "params[notify_mail_address]", 'value' => $vars['entity']->notify_mail_address, 'class' => 'mbm'));