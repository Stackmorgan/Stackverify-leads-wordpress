<?php

if ( ! defined( 'ABSPATH' ) ) exit;



class WP_Webhooks_Integrations_stackverify_Triggers_wpforms_submit {



    public function __construct(){


        add_action(

            'wpforms_process_complete',

            array(
                $this,
                'send_submission'
            ),

            10,

            4

        );


    }





    public function send_submission(

        $fields,

        $entry,

        $form_data,

        $entry_id

    ){



        $stackverify_form_id =
        get_option(
            'stackverify_form_id'
        );



        if(
            empty($stackverify_form_id)
        ){

            return;

        }





        $payload = array();





        foreach(
            $fields as $field
        ){



            if(

                isset($field['name'])

                &&

                isset($field['value'])

            ){


                $payload[

                    sanitize_key(
                        $field['name']
                    )

                ]

                =

                sanitize_text_field(

                    $field['value']

                );


            }


        }





        wp_remote_post(

            'https://stackverify.site/api/f/' .
            $stackverify_form_id,


            array(

                'method'=>'POST',

                'timeout'=>30,


                'headers'=>array(

                    'Content-Type'=>'application/json'

                ),


                'body'=>wp_json_encode(

                    $payload

                )


            )


        );



    }


}
