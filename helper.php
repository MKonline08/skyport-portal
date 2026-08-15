<?php
/**
 * Standard Evil Portal client-info helpers (OpenWrt / dnsmasq on the MK7).
 * Guarded with function_exists so this file can sit alongside the module's
 * own scaffolded helper without a redeclare fatal.
 */

if (!function_exists('getClientMac')) {
    function getClientMac($clientIP) {
        return trim(exec("grep " . escapeshellarg($clientIP) . " /tmp/dhcp.leases | awk '{print $2}'"));
    }
}

if (!function_exists('getClientHostName')) {
    function getClientHostName($clientIP) {
        return trim(exec("grep " . escapeshellarg($clientIP) . " /tmp/dhcp.leases | awk '{print $4}'"));
    }
}

if (!function_exists('getClientSSID')) {
    function getClientSSID($clientIP) {
        return trim(exec("iw dev 2>/dev/null | grep -i ssid | awk '{print $2}' | head -n 1"));
    }
}
