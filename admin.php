<?php

if (!defined('ABSPATH')) exit;

/* =========================
   ADMIN MENU
========================= */
add_action('admin_menu', function () {
    add_menu_page(
        'StackVerify Studio',
        'StackVerify',
        'manage_options',
        'stackverify',
        'stackverify_settings_page',
        'dashicons-randomize',
        80
    );
});

/* =========================
   SETTINGS
========================= */
add_action('admin_init', function () {
    register_setting('stackverify_settings', 'stackverify_form_mapping');
});

/* =========================
   PLUGIN DETECTION (VISUAL ONLY)
========================= */
function stackverify_detect_plugins() {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    return [
        'woocommerce' => class_exists('WooCommerce'),
        'cf7'         => is_plugin_active('contact-form-7/wp-contact-form-7.php'),
        'wpforms'     => is_plugin_active('wpforms-lite/wpforms.php') ||
                         is_plugin_active('wpforms/wpforms.php'),
    ];
}

/* =========================
   LOG EVENTS
========================= */
function stackverify_log_event($type, $data, $status = 'success') {

    $logs = get_option('stackverify_logs', []);

    array_unshift($logs, [
        'type' => $type,
        'data' => $data,
        'status' => $status,
        'time' => current_time('mysql')
    ]);

    $logs = array_slice($logs, 0, 100);

    update_option('stackverify_logs', $logs);
}

/* =========================
   GET FORM IDS
========================= */
function stackverify_get_form_ids($event)
{
    $map = get_option('stackverify_form_mapping', []);

    if (empty($map[$event])) return [];

    $value = $map[$event];

    if (is_string($value)) {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    return (array) $value;
}

/* =========================
   SEND WRAPPER
========================= */
function stackverify_send($event, $data)
{
    if (!class_exists('StackVerifyClient')) return;

    $formIds = stackverify_get_form_ids($event);

    if (empty($formIds)) {
        $formIds = [$event];
    }

    foreach ($formIds as $formId) {

        $res = StackVerifyClient::send($formId, $data);

        $ok = (!is_wp_error($res));

        stackverify_log_event($event, [
            'formId' => $formId,
            'payload' => $data
        ], $ok ? 'success' : 'failed');
    }
}

/* =========================
   TEST EVENT
========================= */
if (isset($_POST['sv_test_event'])) {

    stackverify_send('test_event', [
        'name' => 'Test User',
        'email' => 'test@stackverify.local',
        'message' => 'Studio test event'
    ]);
}

/* =========================
   EVENT HOOKS
========================= */
add_action('woocommerce_thankyou', function ($order_id) {

    if (!function_exists('wc_get_order')) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    stackverify_send('woocommerce_order', [
        'order_id' => $order_id,
        'total'    => $order->get_total(),
        'currency' => $order->get_currency(),
        'email'    => $order->get_billing_email(),
    ]);
});

add_action('wpcf7_mail_sent', function () {

    if (!class_exists('WPCF7_Submission')) return;

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return;

    stackverify_send('cf7_submit', $submission->get_posted_data());
});

add_action('wpforms_process_complete', function ($fields, $entry, $form_data) {

    stackverify_send('wpforms_submit', [
        'fields'  => $fields,
        'form_id' => $form_data['id'] ?? null,
    ]);

}, 10, 3);

add_action('user_register', function ($user_id) {

    $user = get_userdata($user_id);
    if (!$user) return;

    stackverify_send('user_register', [
        'user_id'  => $user_id,
        'email'    => $user->user_email,
        'username' => $user->user_login,
    ]);
});

/* =========================
   UI PAGE (STUDIO)
========================= */
function stackverify_settings_page()
{
    $map = get_option('stackverify_form_mapping', []);
    $logs = get_option('stackverify_logs', []);
    $detected = stackverify_detect_plugins();

    $events = [
        'woocommerce_order',
        'cf7_submit',
        'wpforms_submit',
        'user_register',
        'test_event'
    ];
    ?>

    <div style="max-width:1100px;margin:30px auto;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

        <!-- HEADER -->
        <div style="background:linear-gradient(135deg,#0b5fff,#1d8cff);
                    color:#fff;padding:22px;border-radius:14px;margin-bottom:20px;">
            <h1 style="margin:0;font-size:24px;">StackVerify Studio</h1>
            <div style="opacity:.9;font-size:13px;margin-top:4px;">
                Real-time event routing • webhook intelligence • form automation engine
            </div>
        </div>

        <!-- STATUS PANEL -->
        <div style="display:flex;gap:10px;margin-bottom:20px;">

            <div style="flex:1;background:#fff;border:1px solid #eee;border-radius:10px;padding:12px;">
                <strong>Plugins</strong>
                <div>WooCommerce: <?php echo $detected['woocommerce'] ? '✓' : '—'; ?></div>
                <div>CF7: <?php echo $detected['cf7'] ? '✓' : '—'; ?></div>
                <div>WPForms: <?php echo $detected['wpforms'] ? '✓' : '—'; ?></div>
            </div>

            <div style="flex:2;background:#fff;border:1px solid #eee;border-radius:10px;padding:12px;">
                <strong>Live Engine</strong>
                <div style="color:#1d8cff;">Streaming: Active (UI ready)</div>
                <div>Event Buffer: <?php echo count($logs); ?></div>
            </div>

        </div>

        <!-- EVENT STUDIO -->
        <div style="background:#fff;border:1px solid #eee;border-radius:12px;padding:15px;margin-bottom:20px;">

            <h3 style="margin-top:0;">Event Studio Mapping</h3>
            <p style="font-size:12px;color:#666;">
                Add multiple StackVerify Form IDs per event (comma separated)
            </p>

            <form method="post" action="options.php">
                <?php settings_fields('stackverify_settings'); ?>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">

                    <?php foreach ($events as $event): ?>
                        <div style="border:1px solid #f0f0f0;padding:10px;border-radius:10px;">
                            <label style="font-weight:600;color:#0b5fff;">
                                <?php echo esc_html($event); ?>
                            </label>

                            <input type="text"
                                   name="stackverify_form_mapping[<?php echo esc_attr($event); ?>]"
                                   value="<?php echo esc_attr($map[$event] ?? ''); ?>"
                                   placeholder="frm_leads, frm_orders"
                                   style="width:100%;padding:10px;margin-top:6px;border:1px solid #ddd;border-radius:8px;">
                        </div>
                    <?php endforeach; ?>

                </div>

                <div style="margin-top:15px;">
                    <?php submit_button('Save Studio Mapping'); ?>
                </div>
            </form>
        </div>

        <!-- TEST PANEL -->
        <div style="background:#fff;border:1px solid #eee;border-radius:12px;padding:15px;margin-bottom:20px;">

            <h3>Event Tester</h3>

            <form method="post">
                <input type="hidden" name="sv_test_event" value="1">
                <?php submit_button('Send Test Event → StackVerify'); ?>
            </form>

        </div>

        <!-- LIVE DEBUG PANEL -->
        <div style="background:#0f172a;color:#fff;border-radius:12px;padding:15px;margin-bottom:20px;">

            <h3 style="margin-top:0;">Live Webhook Debug</h3>

            <div style="font-size:12px;opacity:.8;">
                (Streaming UI placeholder — ready for WS / polling upgrade)
            </div>

            <div style="margin-top:10px;max-height:200px;overflow:auto;font-family:monospace;font-size:11px;">

                <?php foreach (array_slice($logs, 0, 10) as $log): ?>
                    <div>
                        [<?php echo esc_html($log['time']); ?>]
                        <?php echo esc_html($log['type']); ?>
                        → <?php echo esc_html($log['status'] ?? 'ok'); ?>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>

        <!-- LOGS -->
        <div style="background:#fff;border:1px solid #eee;border-radius:12px;padding:15px;">

            <h3>Event History</h3>

            <?php if (empty($logs)): ?>
                <div style="color:#999;">No events yet</div>
            <?php else: ?>

                <?php foreach ($logs as $log): ?>
                    <div style="font-size:12px;padding:6px 0;border-bottom:1px solid #f5f5f5;">
                        <strong><?php echo esc_html($log['type']); ?></strong>
                        <span style="color:#888;">
                            — <?php echo esc_html($log['time']); ?>
                        </span>

                        <?php if (($log['status'] ?? '') === 'failed'): ?>
                            <span style="color:red;margin-left:10px;">FAILED</span>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>

    <?php
}
