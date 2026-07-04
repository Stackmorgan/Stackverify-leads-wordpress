=== StackVerify ===
Contributors: stackmorgan
Tags: forms, leads, automation, woocommerce, api, webhook, crm, events
Requires at least: 5.8
Tested up to: 6.6
Stable tag: 1.0.0
License: MIT

== Description ==
StackVerify connects WordPress to your StackVerify automation engine in real time.

It captures and syncs key WordPress events such as WooCommerce orders, contact form submissions, and user registrations directly into your StackVerify workspace using Form IDs.

No backend development required. No custom APIs needed.

Just map your Form IDs and start streaming events instantly.

== Key Benefits ==
- Real-time event sync from WordPress
- Works with WooCommerce, Contact Forms, and Users
- Zero backend configuration required
- Built-in event logging for debugging
- Test event sender for instant validation
- Smart fallback mode for zero-configuration setups

== Installation ==
1. Upload the plugin folder to /wp-content/plugins/
2. Activate the plugin from WordPress admin
3. Open StackVerify settings panel
4. Paste your Form IDs from the StackVerify dashboard
5. Save settings and start syncing events

== How It Works ==
WordPress Event → StackVerify Plugin → StackVerify API → Dashboard + Automations

Each event is sent using a Form ID:

https://stackverify.site/api/f/{formId}

== Features ==
- WooCommerce order tracking
- Contact form integration (CF7 compatible)
- User registration tracking
- Event logging inside WordPress
- Test event sender (for debugging)
- Zero-config fallback mapping system
- Minimal, clean admin UI

== Supported Integrations ==
- WooCommerce
- Contact Form 7
- WordPress user system
- Extensible hooks for custom integrations

== Usage ==
1. Create a form in StackVerify dashboard
2. Copy your Form ID (e.g. frm_orders)
3. Map it inside plugin settings
4. Save and test using “Send Test Event”

== Data Sent ==
This plugin may send the following data to StackVerify:
- Customer information (name, email)
- Order details (if WooCommerce is enabled)
- Form submissions
- User registration metadata

All data is sent only to your configured StackVerify account.

== Privacy ==
This plugin transmits data to StackVerify based on your configuration. No data is stored externally in the plugin itself.

== Changelog ==
= 1.0.0 =
- Initial release
- WooCommerce event support
- Contact form integration
- User registration tracking
- Event logs dashboard
- Test event system
- Zero-config fallback mode
