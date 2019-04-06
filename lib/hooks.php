<?php

function elgg_update_services_cron(\Elgg\Hook $hook) {
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
