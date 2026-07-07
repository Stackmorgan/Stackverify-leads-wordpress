<?php
/*
Plugin Name: StackVerify Connector
Description: Connects WordPress forms and WP Webhooks with StackVerify.
Version: 1.0.0
Author: Morgan Miller
License: GPL-3.0
*/

if (!defined('ABSPATH')) exit;


/**
 * Register StackVerify integration as an external WP Webhooks addon
 */
add_action(
    'plugins_loaded',
    'stackverify_register_wpwebhooks_integration',
    20
);


function stackverify_register_wpwebhooks_integration(){

    // Check if WP Webhooks is installed and active
    if (
        !function_exists('WPWHPRO')
        ||
        !isset(WPWHPRO()->integrations)
    ){

        return;

    }


    $integration_file =
        plugin_dir_path(__FILE__)
        . 'includes/integrations/stackverify/stackverify.php';



    // Register StackVerify integration
    if(
        file_exists($integration_file)
    ){

        WPWHPRO()->integrations->register_integration(

            array(

                'slug' => 'stackverify',

                'path' => $integration_file

            )

        );

    }

}
