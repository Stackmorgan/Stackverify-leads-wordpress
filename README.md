<img src="https://i.ibb.co/Fqgxjt1N/Gemini-Generated-Image-m7irhdm7irhdm7ir.png" alt="StackVerify Logo" width="100%" style="max-width: 900px;">

# StackVerify WordPress Plugin

A lightweight WordPress plugin that fixes one of WordPress’ biggest hidden problems:

> WordPress struggles to reliably turn events (orders, forms, users) into real-time communication and automation.

This plugin connects WordPress events directly to external communication systems using simple Form IDs.

No backend required.

---

## 🚨 The Problem in WordPress

Most WordPress sites face the same issues:

- Contact forms sit idle with no real-time follow-up
- WooCommerce orders are not instantly acted on
- User registrations are ignored after signup
- Teams rely on email inboxes instead of automation
- No central event pipeline for communication

Result:
> Lost leads, slow response times, and broken customer communication.

---

## ⚡ The Solution

StackVerify WordPress Plugin turns WordPress into a real-time communication engine.

Every important event becomes a structured signal:

- Order placed → instant event
- Form submitted → instant event
- User registered → instant event

These events are sent to StackVerify using Form IDs and can trigger:

- Email notifications
- WhatsApp messages
- Webhooks
- CRM workflows
- Internal automation systems

---

## 🚀 What it enables

Instead of manually checking WordPress:

✔ Customers get instant responses  
✔ Sales teams get real-time leads  
✔ Orders trigger immediate workflows  
✔ No missed communication  

---

## 📦 Installation

### 1. Download plugin

From GitHub Releases:

https://github.com/stackmorgan/stackverify-wordpress/releases/latest

---

### 2. Install in WordPress

- Go to: Plugins → Add New → Upload Plugin
- Upload ZIP
- Activate

---

## ⚙️ Setup (1–2 minutes)

1. Create a form in StackVerify dashboard
2. Copy Form ID (example: `frm_orders`)
3. Open WordPress → StackVerify settings
4. Map events:
   - WooCommerce Orders
   - Contact Forms
   - User Registrations
5. Save

Done.

---

## 🔁 How it works

```text
WordPress Event Happens
        ↓
StackVerify Plugin captures event
        ↓
Event is sent via Form ID
        ↓
StackVerify processes it
        ↓
Triggers communication (email, WhatsApp, webhook, automation)
```
## 📡 Supported WordPress Events

The plugin currently supports:

- WooCommerce Orders
- Contact Form Submissions
- User Registrations

More integrations can be added via WordPress hooks and lightweight extensions.

---

## 🧪 Test Mode

Inside the WordPress admin dashboard, you can send a test event:

> StackVerify → Send Test Event

This is used to verify:

- Plugin is correctly installed
- Form IDs are valid
- WordPress is communicating with StackVerify
- Event mapping is working as expected

---

## 🧠 Design Philosophy

This plugin is built on a simple principle:

> WordPress should generate events, not manage communication logic.

Instead of building and maintaining:
- Email systems
- Webhook layers
- CRM integrations
- Notification pipelines

WordPress only focuses on capturing events, while StackVerify handles delivery and automation.

This keeps WordPress:
- Lightweight
- Faster
- Easier to maintain
- More scalable for real businesses

---

## 🔒 Privacy

- This plugin does NOT store user data externally
- No tracking or background analytics
- Data is only sent when events occur
- You fully control what is transmitted via Form IDs
- No hidden requests or silent data collection

---

## 📦 Releases

Latest stable versions are available here:

https://github.com/stackmorgan/stackverify-wordpress/releases

Each release includes a downloadable ZIP file for direct WordPress installation.

---

## 🧩 Roadmap

Future improvements planned:

- Auto-detect WooCommerce installation
- Auto-detect Contact Form 7 and WPForms
- Background queue for high-volume stores
- Retry system for failed event delivery
- Advanced event logs dashboard inside WordPress
- One-click StackVerify onboarding connection
- Zero-configuration mode (smart defaults)

---

## 📄 License

MIT License

You are free to use, modify, and distribute this plugin.

---

## 🌐 Links

- StackVerify Platform: https://stackverify.site
- StackVerify Forms SDK: https://www.npmjs.com/package/@stackverify/forms
- GitHub Repository: https://github.com/stackmorgan/stackverify-wordpress
