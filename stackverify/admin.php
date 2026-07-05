<?php
if (!defined('ABSPATH')) exit;

class StackVerify_Admin {

    private $option_name = 'stackverify_event_logs';
    private $settings_name = 'stackverify_settings';

    public function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_stackverify_save_settings', [$this, 'save_settings']);
    }

    public function menu() {
        add_menu_page(
            'StackVerify',
            'StackVerify',
            'manage_options',
            'stackverify',
            [$this, 'render'],
            'dashicons-shield',
            56
        );
    }

    /* =========================
     * SETTINGS
     * ========================= */

    private function get_settings() {
        return get_option($this->settings_name, [
            'webhook_url' => 'https://stackverify.site/api/f/default',
            'api_key'     => ''
        ]);
    }

    public function save_settings() {
        if (!current_user_can('manage_options')) return;

        $data = [
            'webhook_url' => esc_url_raw($_POST['webhook_url']),
            'api_key'     => sanitize_text_field($_POST['api_key']),
        ];

        update_option($this->settings_name, $data);

        wp_redirect(admin_url('admin.php?page=stackverify&saved=1'));
        exit;
    }

    /* =========================
     * EVENT LOGS (TEMP STORAGE)
     * ========================= */

    private function get_logs() {
        return get_option($this->option_name, []);
    }

    public static function add_log($log) {
        $logs = get_option('stackverify_event_logs', []);

        $logs[] = [
            'event'    => $log['event'] ?? 'unknown',
            'source'   => $log['source'] ?? 'system',
            'status'   => $log['status'] ?? 'sent',
            'time'     => current_time('mysql')
        ];

        // keep only last 50
        $logs = array_slice($logs, -50);

        update_option('stackverify_event_logs', $logs);
    }

    /* =========================
     * RENDER UI
     * ========================= */

    public function render() {

        $s = $this->get_settings();
        $logs = $this->get_logs();

        ?>

<style>
:root {
  --bg:#FAFAFA;
  --card:#fff;
  --text:#111827;
  --muted:#6B7280;
  --accent:#2563EB;
  --border:#E5E7EB;
  --good:#059669;
  --bad:#DC2626;
}

.wrap {
  background: var(--bg);
  padding: 20px;
  min-height: 100vh;
  color: var(--text);
}

.card, .panel {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
  gap: 15px;
}

.stat {
  font-size: 28px;
  font-weight: 700;
}

label {
  display:block;
  margin-top:10px;
  font-weight:500;
}

input {
  width:100%;
  padding:10px;
  border:1px solid var(--border);
  border-radius:8px;
}

button {
  background: var(--accent);
  color:#fff;
  border:none;
  padding:10px 14px;
  border-radius:8px;
  cursor:pointer;
}

.badge {
  padding:4px 10px;
  border-radius:20px;
  font-size:12px;
}

.sent { background:#DCFCE7; color:var(--good); }
.failed { background:#FEE2E2; color:var(--bad); }

table {
  width:100%;
  border-collapse: collapse;
}

td,th {
  padding:12px;
  border-bottom:1px solid var(--border);
  text-align:left;
}

th {
  font-size:12px;
  color:var(--muted);
  text-transform:uppercase;
}
</style>

<div class="wrap">

  <!-- HEADER -->
  <div class="card">
    <h2>StackVerify Engine</h2>
    <p style="color:var(--muted)">
      Universal event capture system → WordPress → StackVerify server
    </p>
  </div>

  <!-- STATS (REAL PLACEHOLDER) -->
  <div class="grid">

    <div class="card">
      <h3>Total Events</h3>
      <div class="stat"><?php echo count($logs); ?></div>
    </div>

    <div class="card">
      <h3>Sent</h3>
      <div class="stat">
        <?php echo count(array_filter($logs, fn($l) => $l['status'] === 'sent')); ?>
      </div>
    </div>

    <div class="card">
      <h3>Failed</h3>
      <div class="stat">
        <?php echo count(array_filter($logs, fn($l) => $l['status'] === 'failed')); ?>
      </div>
    </div>

  </div>

  <!-- SETTINGS -->
  <div class="panel">

    <h3>Global Settings</h3>

    <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
      <input type="hidden" name="action" value="stackverify_save_settings">

      <label>StackVerify Endpoint</label>
      <input name="webhook_url" value="<?php echo esc_attr($s['webhook_url']); ?>">

      <label>API Key (optional)</label>
      <input name="api_key" value="<?php echo esc_attr($s['api_key']); ?>">

      <button type="submit">Save</button>
    </form>

  </div>

  <!-- TEST -->
  <div class="panel">

    <h3>Test Event</h3>

    <button onclick="fetch(window.location.href + '&test=1')">
      Send Test Event
    </button>

  </div>

  <!-- LOGS -->
  <div class="panel">

    <h3>Event Stream</h3>

    <table>
      <thead>
        <tr>
          <th>Event</th>
          <th>Source</th>
          <th>Status</th>
          <th>Time</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($logs)): ?>
          <tr><td colspan="4">No events yet</td></tr>
        <?php else: ?>
          <?php foreach (array_reverse($logs) as $log): ?>
            <tr>
              <td><?php echo esc_html($log['event']); ?></td>
              <td><code><?php echo esc_html($log['source']); ?></code></td>
              <td>
                <span class="badge <?php echo esc_attr($log['status']); ?>">
                  <?php echo esc_html($log['status']); ?>
                </span>
              </td>
              <td><?php echo esc_html($log['time']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>

    </table>

  </div>

</div>

<?php
    }
}

new StackVerify_Admin();
