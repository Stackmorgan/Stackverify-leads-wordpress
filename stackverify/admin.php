<?php

if (!defined('ABSPATH')) exit;

/* =========================
   ADMIN MENU
========================= */
add_action('admin_menu', function () {
    add_menu_page(
        'StackVerify',
        'StackVerify',
        'manage_options',
        'stackverify',
        'stackverify_settings_page',
        'dashicons-filter',
        80
    );
});

/* =========================
   SETTINGS
========================= */
add_action('admin_init', function () {
    register_setting('stackverify_settings', 'stackverify_api_key');
    register_setting('stackverify_settings', 'stackverify_mappings');
});

/* =========================
   DETECT PLUGINS
========================= */
function stackverify_detect_plugins() {
    return [
        'woocommerce' => class_exists('WooCommerce'),
        'cf7'         => class_exists('WPCF7'),
    ];
}

/* =========================
   DEFAULT FORM IDS (ZERO CONFIG)
========================= */
function stackverify_default_form_id($type) {

    $defaults = [
        'woocommerce' => 'frm_orders',
        'contact'     => 'frm_contact',
        'user'        => 'frm_users'
    ];

    return $defaults[$type] ?? null;
}

/* =========================
   RESOLVE FORM ID
========================= */
function stackverify_get_form_id($type) {

    $m = get_option('stackverify_mappings', []);

    return $m[$type] ?? stackverify_default_form_id($type);
}

/* =========================
   LOGS
========================= */
function stackverify_log_event($type, $data) {

    $logs = get_option('stackverify_logs', []);

    array_unshift($logs, [
        'type' => $type,
        'data' => $data,
        'time' => current_time('mysql')
    ]);

    $logs = array_slice($logs, 0, 20);

    update_option('stackverify_logs', $logs);
}

/* =========================
   TEST EVENT
========================= */
if (isset($_POST['sv_test'])) {

    $formId = stackverify_get_form_id('contact');

    if ($formId) {
        StackVerifyClient::send($formId, [
            'name' => 'Test User',
            'email' => 'test@stackverify.local',
            'message' => 'Test event from WordPress plugin'
        ]);

        stackverify_log_event('test', ['formId' => $formId]);
    }
}

/* =========================
   UI PAGE
========================= */
function stackverify_settings_page() {

    $m = get_option('stackverify_mappings', []);
    $logs = get_option('stackverify_logs', []);
    $detected = stackverify_detect_plugins();

    ?>

    <div style="max-width:800px; margin:40px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

        <!-- HEADER -->
        <h1 style="font-weight:500; margin-bottom:25px;">
            StackVerify
        </h1>

        <!-- DETECTION -->
        <div style="padding:12px; border:1px solid #eee; border-radius:6px; margin-bottom:25px;">

            <div style="font-size:13px; margin-bottom:8px; color:#666;">
                System Status
            </div>

            <div style="font-size:13px;">
                WooCommerce:
                <strong style="color:<?php echo $detected['woocommerce'] ? 'green' : '#999'; ?>">
                    <?php echo $detected['woocommerce'] ? 'Detected ✓' : 'Not Found'; ?>
                </strong>
            </div>

            <div style="font-size:13px;">
                Contact Form 7:
                <strong style="color:<?php echo $detected['cf7'] ? 'green' : '#999'; ?>">
                    <?php echo $detected['cf7'] ? 'Detected ✓' : 'Not Found'; ?>
                </strong>
            </div>

        </div>

        <!-- FORM -->
        <form method="post" action="options.php">

            <?php settings_fields('stackverify_settings'); ?>

            <!-- API KEY -->
            <div style="margin-bottom:20px;">
                <label style="font-size:12px; color:#666;">API Key (optional)</label>
                <input type="text"
                       name="stackverify_api_key"
                       value="<?php echo esc_attr(get_option('stackverify_api_key')); ?>"
                       style="width:100%; padding:10px; border:1px solid #eee; border-radius:6px;">
            </div>

            <hr style="border:none; border-top:1px solid #eee; margin:25px 0;">

            <h2 style="font-size:13px; font-weight:500; margin-bottom:15px;">
                Event Mapping
            </h2>

            <!-- Woo -->
            <div style="margin-bottom:15px;">
                <label style="font-size:12px; color:#666;">WooCommerce Orders → Form ID</label>
                <input type="text"
                       name="stackverify_mappings[woocommerce]"
                       value="<?php echo esc_attr($m['woocommerce'] ?? ''); ?>"
                       placeholder="frm_orders"
                       style="width:100%; padding:10px; border:1px solid #eee; border-radius:6px;">
            </div>

            <!-- Contact -->
            <div style="margin-bottom:15px;">
                <label style="font-size:12px; color:#666;">Contact Forms → Form ID</label>
                <input type="text"
                       name="stackverify_mappings[contact]"
                       value="<?php echo esc_attr($m['contact'] ?? ''); ?>"
                       placeholder="frm_contact"
                       style="width:100%; padding:10px; border:1px solid #eee; border-radius:6px;">
            </div>

            <!-- Users -->
            <div style="margin-bottom:25px;">
                <label style="font-size:12px; color:#666;">User Registrations → Form ID</label>
                <input type="text"
                       name="stackverify_mappings[user]"
                       value="<?php echo esc_attr($m['user'] ?? ''); ?>"
                       placeholder="frm_users"
                       style="width:100%; padding:10px; border:1px solid #eee; border-radius:6px;">
            </div>

            <?php submit_button('Save Settings'); ?>

        </form>

        <!-- TEST BUTTON -->
        <form method="post" style="margin-top:15px;">
            <input type="hidden" name="sv_test" value="1">
            <?php submit_button('Send Test Event → StackVerify'); ?>
        </form>

        <!-- LOGS -->
        <div style="margin-top:30px;">

            <h3 style="font-size:13px; font-weight:500; margin-bottom:10px;">
                Recent Events
            </h3>

            <div style="border:1px solid #eee; border-radius:6px; padding:10px;">

                <?php if (empty($logs)): ?>

                    <div style="font-size:12px; color:#999;">
                        No events yet
                    </div>

                <?php else: ?>

                    <?php foreach ($logs as $log): ?>

                        <div style="font-size:12px; padding:6px 0; border-bottom:1px solid #f5f5f5;">
                            <strong><?php echo esc_html($log['type']); ?></strong>
                            <span style="color:#999;">— <?php echo esc_html($log['time']); ?></span>
                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>
        </div>

        <p style="margin-top:25px; font-size:11px; color:#aaa;">
            StackVerify syncs WordPress events using Form IDs from your dashboard.
        </p>

    </div>

    <?php
}
