<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

delete_option('stackverify_api_key');
delete_option('stackverify_mappings');
delete_option('stackverify_logs');
