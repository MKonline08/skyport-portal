# SkyPort Guest Wi-Fi — Evil Portal for WiFi Pineapple MK7

A clean, modern captive portal for the Evil Portal module. Mobile-first design
(most clients hit it from a phone's captive-network popup), fully self-contained
(no external assets, since clients have no internet until after auth), and a
realistic flow: submit → "Connecting…" spinner → client is authorized and gets
real internet → "You're connected" screen → redirect to google.com.

**Authorized testing only** — run this on networks you own or where you have
written permission.

## Files

| File          | Purpose                                                        |
|---------------|----------------------------------------------------------------|
| `index.php`   | Landing page. Rebrand via `$BRAND`, `$SSID`, `$ACCENT` on top. |
| `MyPortal.php`| Capture backend. Logs JSON lines, notifies the module UI.      |
| `helper.php`  | Client MAC / hostname / SSID lookups from dnsmasq leases.      |

## Install

1. In the Pineapple web UI, open the **Evil Portal** module and create a new
   portal named `skyport`. This scaffolds `/root/portals/skyport/` including
   the `skyport.ep` rules file — keep that file.
2. Copy these three files over the scaffold:

   ```bash
   scp index.php MyPortal.php helper.php root@172.16.42.1:/root/portals/skyport/
   ```

3. In the module UI: activate the **skyport** portal, then **Start** Evil Portal.
4. Have PineAP broadcast the SSID you set in `$SSID` (or update `$SSID` to match
   your PineAP setup) so clients associate.

## Where captures land

- **JSON lines:** `/root/logs/skyport_portal.log` (or `/sd/logs/` if an SD card
  is installed) — one JSON object per submission with email, password, MAC,
  hostname, SSID, IP, user agent, timestamp.
- **Module log viewer:** each capture also goes through `$this->writeLog()`.
- **Web UI notifications:** `$this->notify()` pings the dashboard per capture.

## Customization notes

- Change brand/SSID/color in three variables at the top of `index.php`.
- To clone a specific organization's portal instead, save the target page as a
  single HTML file (e.g. with the SingleFile browser extension), rename it
  `index.php`, paste this portal's PHP header block at the top, and point its
  form at `/captiveportal/index.php` with the same hidden fields
  (`mac`, `host`, `ssid`, `target`) plus `email`/`password` inputs.
- The backend intentionally keeps captures local to the device — pull the log
  over SSH when the engagement is done.

Tested-against: Evil Portal module on MK7 firmware 2.x (the
`index.php` / `MyPortal.php` / `helper.php` skeleton and
`/captiveportal/index.php` POST routing).
