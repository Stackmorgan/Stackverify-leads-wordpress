<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class WP_Webhooks_Integrations_stackverify {


    public $actions = array();

    public $triggers = array();

    public $helpers = array();



    public function __construct(){


        /*
         * Load admin connection page
         */
        require_once __DIR__ . '/admin/settings.php';

        new StackVerify_Admin_Settings();



        /*
         * Load actions
         */
        require_once __DIR__ . '/actions/send_form.php';


        $this->actions = (object) array();


        $this->actions->stackverify_send_form =
        new WP_Webhooks_Integrations_stackverify_Actions_send_form();




        /*
         * Load triggers
         */
        require_once __DIR__ . '/triggers/wpforms_submit.php';


        $this->triggers = (object) array();


        $this->triggers->stackverify_wpforms_submit =
        new WP_Webhooks_Integrations_stackverify_Triggers_wpforms_submit();


    }





    public function is_active(){

        return true;

    }





    public function get_details(){

        return array(

            'name' => 'StackVerify',

            'icon' => plugin_dir_url(__FILE__) . 'assets/icon.png'

        );

    }


}
