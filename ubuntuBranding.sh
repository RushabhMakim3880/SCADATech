#!/bin/bash
set -e

LOGO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/public/assets/img/ubuntuLogos"

# Files in your custom logo folder
CUSTOM_PLYMOUTH="$LOGO_DIR/ubuntu-logo.png"
CUSTOM_FAVICON="$LOGO_DIR/cropped-favicon.png"
CUSTOM_WALLPAPER="$LOGO_DIR/wallpaper.jpg"

# Target system files
PLYMOUTH_LOGO="/usr/share/plymouth/ubuntu-logo.png"
SPINNER_WATERMARK="/usr/share/plymouth/themes/spinner/watermark.png"
SPINNER_FALLBACK="/usr/share/plymouth/themes/spinner/bgrt-fallback.png"
LOGIN_BG="/usr/share/backgrounds/login-bg.jpg"

echo "🎨 Updating Plymouth boot splash..."

# Backup originals (once)
[ ! -f "${PLYMOUTH_LOGO}.bkp" ] && sudo cp "$PLYMOUTH_LOGO" "${PLYMOUTH_LOGO}.bkp"
[ ! -f "${SPINNER_WATERMARK}.bkp" ] && sudo cp "$SPINNER_WATERMARK" "${SPINNER_WATERMARK}.bkp"
[ ! -f "${SPINNER_FALLBACK}.bkp" ] && sudo cp "$SPINNER_FALLBACK" "${SPINNER_FALLBACK}.bkp"

# Apply new logos
sudo cp "$CUSTOM_PLYMOUTH" "$PLYMOUTH_LOGO"
sudo cp "$CUSTOM_PLYMOUTH" "$SPINNER_WATERMARK"
sudo cp "$CUSTOM_FAVICON" "$SPINNER_FALLBACK"

# Rebuild initramfs
echo "🔄 Rebuilding initramfs..."
sudo update-initramfs -u

echo "🖼️ Setting desktop wallpaper..."
gsettings set org.gnome.desktop.background picture-uri "file://$CUSTOM_WALLPAPER"
gsettings set org.gnome.desktop.background picture-options "zoom"

echo "🔒 Setting GDM login screen wallpaper..."
sudo cp "$CUSTOM_WALLPAPER" "$LOGIN_BG"

GDM_CONF="/etc/dconf/db/gdm.d/01-custom-settings"
sudo mkdir -p "$(dirname "$GDM_CONF")"

sudo bash -c "cat > $GDM_CONF" <<EOF
[org/gnome/desktop/background]
picture-uri='file://$LOGIN_BG'
picture-options='zoom'
EOF

sudo dconf update
sudo systemctl restart gdm

echo "✅ All branding applied. Reboot to see full effect."
