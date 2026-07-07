<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class WP_Webhooks_Integrations_stackverify_Actions_send_form {



    public function get_details(){


        return array(

            'action' => 'stackverify_send_form',

            'name' => 'Send Submission To StackVerify',

            'integration' => 'stackverify',


            'parameter' => array(

                'stackverify_form_id' => array(

                    'label' => 'StackVerify Form ID',

                    'type' => 'text',

                    'required' => false,

                    'description' =>
                    'Your StackVerify form ID. Example: frm_ERex6qDoGF'

                )

            )


        );


    }





    public function execute($return_data, $response_body){



        /*
         * WP Webhooks passes the trigger output here.
         * This can contain data from:
         *
         * WPForms
         * Elementor Forms
         * Contact Form 7
         * Gravity Forms
         * Other integrations
         */



        $form_id = '';



        /*
         * First check action setting
         */

        if(
            isset($response_body['stackverify_form_id'])
        ){

            $form_id = sanitize_text_field(
                $response_body['stackverify_form_id']
            );

        }




        /*
         * If empty use global admin connection
         */

        if(empty($form_id)){


            $form_id = get_option(
                'stackverify_form_id',
                ''
            );


        }




        if(empty($form_id)){


            $return_data['error'] =
            'StackVerify Form ID is missing';


            return $return_data;


        }





        /*
         * Remove plugin configuration
         * before sending real form data
         */

        unset(
            $response_body['stackverify_form_id']
        );





        /*
         * Send submission to StackVerify
         */

        $request = wp_remote_post(

            'https://stackverify.site/api/f/' . $form_id,


            array(

                'method' => 'POST',

                'timeout' => 30,


                'headers' => array(

                    'Content-Type' => 'application/json'

                ),


                'body' => wp_json_encode(
                    $response_body
                )


            )

        );





        if(
            is_wp_error($request)
        ){


            $return_data['error'] =
            $request->get_error_message();


            return $return_data;


        }




        $return_data['success'] = true;


        $return_data['stackverify'] = array(

            'form_id' => $form_id,

            'status' => 'sent'

        );



        return $return_data;


    }



}
