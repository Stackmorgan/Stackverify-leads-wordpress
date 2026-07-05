<?php

if (!defined('ABSPATH')) exit;

/**
 * StackVerify Events Collector
 * Universal WordPress event capture layer
 */

class StackVerify_Events
{
    public function __construct()
    {
        // WordPress core events
        add_action('user_register', [$this, 'user_registered'], 10, 1);
        add_action('wp_login', [$this, 'user_login'], 10, 2);
        add_action('profile_update', [$this, 'profile_updated'], 10, 2);

        // WooCommerce (only if exists)
        add_action('init', [$this, 'maybe_hook_woocommerce']);

        // Contact Form 7 (only if exists)
        add_action('init', [$this, 'maybe_hook_cf7']);
    }

    /**
     * Generic sender wrapper
     */
    private function send($formId, $data, $eventType)
    {
        if (!class_exists('StackVerifyClient')) {
            return;
        }

        $payload = [
            'event_type' => $eventType,
            'site_url'   => get_site_url(),
            'data'       => $data,
            'timestamp'  => current_time('mysql'),
        ];

        StackVerifyClient::send($formId, $payload);
    }

    /* =========================
     * WP CORE EVENTS
     * ========================= */

    public function user_registered($user_id)
    {
        $user = get_userdata($user_id);

        if (!$user) return;

        $this->send('wp_user_register', [
            'user_id' => $user_id,
            'email'   => $user->user_email,
            'roles'   => $user->roles,
        ], 'user.registered');
    }

    public function user_login($user_login, $user)
    {
        $this->send('wp_user_login', [
            'user_id' => $user->ID,
            'email'   => $user->user_email,
            'login'   => $user_login,
        ], 'user.login');
    }

    public function profile_updated($user_id, $old_user_data)
    {
        $user = get_userdata($user_id);

        if (!$user) return;

        $this->send('wp_profile_update', [
            'user_id' => $user_id,
            'email'   => $user->user_email,
        ], 'user.profile_updated');
    }

    /* =========================
     * WOOCOMMERCE EVENTS (SAFE HOOKING)
     * ========================= */

    public function maybe_hook_woocommerce()
    {
        if (!function_exists('wc_get_order')) {
            return;
        }

        add_action('woocommerce_thankyou', [$this, 'order_created'], 10, 1);
    }

    public function order_created($order_id)
    {
        if (!function_exists('wc_get_order')) return;

        $order = wc_get_order($order_id);
        if (!$order) return;

        $this->send('wc_order', [
            'order_id' => $order_id,
            'total'    => $order->get_total(),
            'currency' => $order->get_currency(),
            'email'    => $order->get_billing_email(),
            'status'   => $order->get_status(),
        ], 'woocommerce.order_created');
    }

    /* =========================
     * CONTACT FORM 7 (SAFE)
     * ========================= */

    public function maybe_hook_cf7()
    {
        if (!class_exists('WPCF7_Submission')) {
            return;
        }

        add_action('wpcf7_mail_sent', [$this, 'cf7_submitted']);
    }

    public function cf7_submitted($contact_form)
    {
        $submission = \WPCF7_Submission::get_instance();
        if (!$submission) return;

        $data = $submission->get_posted_data();

        $this->send('cf7_form', $data, 'contact.form_submitted');
    }
}

/**
 * Initialize
 */
new StackVerify_Events();
