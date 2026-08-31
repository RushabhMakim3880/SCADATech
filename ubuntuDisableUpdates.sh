#!/usr/bin/env bash
# ubuntu22-stop-auto-updates.sh
# Run once as root to stop ALL background update checks & nags (APT, timers, PackageKit, GUI prompts, snap).

set -euo pipefail

require_root() { [ "$(id -u)" -eq 0 ] || { echo "Run as root (sudo $0)"; exit 1; }; }
bk() { [ -f "$1" ] && cp -n "$1" "$1.bak.$(date +%s)" || true; }

disable_apt_periodic() {
  mkdir -p /etc/apt/apt.conf.d
  for f in /etc/apt/apt.conf.d/10periodic /etc/apt/apt.conf.d/20auto-upgrades; do
    [ -f "$f" ] && bk "$f"
  done
  cat >/etc/apt/apt.conf.d/10periodic <<'EOF'
APT::Periodic::Enable "0";
APT::Periodic::Update-Package-Lists "0";
APT::Periodic::Download-Upgradeable-Packages "0";
APT::Periodic::AutocleanInterval "0";
APT::Periodic::Unattended-Upgrade "0";
EOF
  cat >/etc/apt/apt.conf.d/20auto-upgrades <<'EOF'
APT::Periodic::Update-Package-Lists "0";
APT::Periodic::Unattended-Upgrade "0";
EOF
}

disable_apt_timers() {
  systemctl disable --now apt-daily.timer apt-daily-upgrade.timer || true
  systemctl mask apt-daily.service apt-daily-upgrade.service || true
}

disable_unattended_upgrades() {
  systemctl disable --now unattended-upgrades.service || true
  systemctl mask unattended-upgrades.service || true
}

disable_packagekit() {
  systemctl stop packagekit || true
  systemctl disable packagekit || true
  systemctl mask packagekit || true
}

disable_update_notifier() {
  # Remove packages if present (safe; keeps dependencies)
  DEBIAN_FRONTEND=noninteractive apt-get -y remove update-notifier update-notifier-common || true
  # Also prevent autostart if any stub remains
  if [ -f /etc/xdg/autostart/update-notifier.desktop ]; then
    mv /etc/xdg/autostart/update-notifier.desktop /etc/xdg/autostart/update-notifier.desktop.disabled || true
  fi
}

# OPTIONAL: stop snap auto-refresh (choose ONE of the following modes)
stop_snap_autorefresh_soft() {
  # Defer snap refresh a long time (you can re-run later). Also stop repair timer.
  if command -v snap >/dev/null 2>&1; then
    snap set system refresh.timer=thu6,23:59-23:59 2>/dev/null || true   # effectively never
    snap set system refresh.hold="$(date -d '2099-01-01 00:00:00' --iso-8601=seconds)" 2>/dev/null || true
    systemctl disable --now snapd.snap-repair.timer || true
  fi
}
stop_snap_autorefresh_hard() {
  # Completely remove snapd (reversible but disruptive). Comment this out if you don't want it.
  if command -v snap >/dev/null 2>&1; then
    systemctl disable --now snapd.service snapd.socket snapd.seeded.service || true
    DEBIAN_FRONTEND=noninteractive apt-get -y purge snapd || true
    rm -rf /snap /var/snap /var/lib/snapd || true
  fi
}

# OPTIONAL: quiet login MOTD news timers (not updates, but nags)
quiet_motd_news() {
  systemctl disable --now motd-news.timer 2>/dev/null || true
  [ -f /etc/default/motd-news ] && { bk /etc/default/motd-news; sed -i 's/^ENABLED=.*/ENABLED=0/' /etc/default/motd-news; } || true
}

main() {
  require_root
  disable_apt_periodic
  disable_apt_timers
  disable_unattended_upgrades
  disable_packagekit
  disable_update_notifier

  # Choose ONE snap option:
  stop_snap_autorefresh_soft
  # stop_snap_autorefresh_hard   # <- enable instead of soft if you want snapd removed

  quiet_motd_news

  systemctl daemon-reload || true
  echo "✔ Background update checks & prompts disabled. You can still update manually with: apt update && apt upgrade"
}

main "$@"
