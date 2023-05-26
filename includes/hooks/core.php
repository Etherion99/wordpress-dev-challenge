<?php

if ( ! defined('ABSPATH') )
    die('Direct access not permitted.');

// Functions for hooks

// Register wp schedule event for link analysis cron job
function register_link_analysis_cron(): void {
	if (!wp_next_scheduled('link_analysis_cron')) {
		wp_schedule_event(time(), 'link_analysis_interval', 'link_analysis_cron');
	}
}

// Create custom cron intervals
function custom_cron_schedules( $schedules ) {
	$schedules['link_analysis_interval'] = array(
		'interval' => 1800,
		'display' => __('Link Analysis Interval', 'etherion-tools')
	);
	return $schedules;
}

// Hooks configuration
add_filter( 'cron_schedules', 'custom_cron_schedules');
add_action( 'link_analysis_cron', 'find_broken_links' );