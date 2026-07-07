<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class StackVerify_Admin_Settings {


    public function __construct(){

        add_action(
            'admin_menu',
            array($this, 'menu')
        );


        add_action(
            'admin_init',
            array($this, 'save')
        );

    }



    public function menu(){

        add_options_page(

            'StackVerify Connection',

            'StackVerify',

            'manage_options',

            'stackverify',

            array($this,'page')

        );

    }




    public function page(){

        $form_id = get_option(
            'stackverify_form_id',
            ''
        );

        ?>

        <div class="wrap" style="max-width: 800px; margin-top: 20px;">

            <!-- Saved Confirmation Notice -->
            <?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) : ?>
                <div class="notice notice-success is-dismissible" style="margin: 0 0 20px 0; border-radius: 6px;">
                    <p>StackVerify connection saved successfully.</p>
                </div>
            <?php endif; ?>

            <!-- Header Banner -->
            <div style="
                background: #ffffff;
                padding: 24px 30px;
                border-radius: 8px;
                margin-bottom: 20px;
                border: 1px solid #e2e8f0;
            ">
                <h1 style="
                    color: #1e293b;
                    margin: 0 0 8px 0;
                    font-size: 24px;
                    font-weight: 600;
                ">
                    StackVerify Connector
                </h1>

                <p style="
                    font-size: 14px;
                    color: #64748b;
                    margin: 0;
                    line-height: 1.5;
                ">
                    Connect WordPress forms with StackVerify using WP Webhooks. 
                    This connector adds StackVerify support while keeping your existing WP Webhooks setup unchanged.
                </p>
            </div>

            <!-- Main Form Card -->
            <div style="
                background: #ffffff;
                padding: 30px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            ">

                <h2 style="
                    font-size: 18px;
                    font-weight: 600;
                    color: #0f172a;
                    margin: 0 0 8px 0;
                ">
                    Connection Settings
                </h2>

                <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">
                    Enter your StackVerify Form ID to automatically send incoming form submissions to your StackVerify workflows.
                </p>

                <form method="post" action="">

                    <?php
                    wp_nonce_field(
                        'stackverify_save',
                        'stackverify_nonce'
                    );
                    ?>

                    <div style="margin-bottom: 24px;">
                        <label style="
                            display: block; 
                            font-weight: 500; 
                            color: #334155; 
                            margin-bottom: 8px;
                            font-size: 14px;
                        ">
                            StackVerify Form ID
                        </label>

                        <input 
                            type="text" 
                            name="stackverify_form_id" 
                            value="<?php echo esc_attr($form_id); ?>" 
                            placeholder="frm_ERex6qDoGF" 
                            style="
                                padding: 10px 14px;
                                width: 100%;
                                max-width: 400px;
                                border: 1px solid #cbd5e1;
                                border-radius: 6px;
                                font-size: 14px;
                                background-color: #f8fafc;
                                color: #334155;
                                box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
                            "
                        >

                        <p class="description" style="margin-top: 6px; color: #94a3b8; font-size: 12px;">
                            Example: <code>frm_ERex6qDoGF</code>
                        </p>
                    </div>

                    <?php
                    submit_button(
                        'Save StackVerify Connection',
                        'primary',
                        'submit',
                        true,
                        array(
                            'style' => 'background: #2563eb; border-color: #2563eb; border-radius: 6px; padding: 6px 16px; font-weight: 500; height: auto;'
                        )
                    );
                    ?>

                </form>

            </div>

            <!-- How It Works Section -->
            <div style="
                margin-top: 20px;
                padding: 24px;
                background: #f8fafc;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
            ">
                <h3 style="
                    font-size: 15px;
                    font-weight: 600;
                    color: #1e293b;
                    margin: 0 0 12px 0;
                ">
                    How StackVerify Connector Works
                </h3>

                <ol style="
                    margin: 0; 
                    padding-left: 20px; 
                    color: #475569; 
                    font-size: 13px;
                    line-height: 1.8;
                ">
                    <li>Your visitor submits a WordPress form.</li>
                    <li>WP Webhooks receives the form submission.</li>
                    <li>StackVerify Connector forwards the data to StackVerify.</li>
                    <li>StackVerify processes the submission and sends notifications.</li>
                </ol>
            </div>

        </div>

        <?php

    }




    public function save(){


        if(
            !isset($_POST['stackverify_nonce'])
        ){
            return;
        }



        if(
            !wp_verify_nonce(
                sanitize_text_field($_POST['stackverify_nonce']),
                'stackverify_save'
            )
        ){

            return;

        }



        if(
            !current_user_can('manage_options')
        ){

            return;

        }



        if (
            isset($_POST['stackverify_form_id'])
        ){

            update_option(

                'stackverify_form_id',

                sanitize_text_field(
                    $_POST['stackverify_form_id']
                )

            );

            // Redirect back to settings page with update confirmation argument
            wp_redirect(
                add_query_arg(
                    array( 'page' => 'stackverify', 'settings-updated' => 'true' ),
                    admin_url( 'options-general.php' )
                )
            );
            exit;

        }


    }


}
