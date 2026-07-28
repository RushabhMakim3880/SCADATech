#!/bin/bash

# Config
APP_NAME="scada-node"
NODE_APP_PATH="/var/www/html/hptinnovanceanglepunchinghead6/nodeOpMaster"
NODE_EXEC="/usr/bin/node"
USER="www-data"
SERVICE_FILE="/etc/systemd/system/$APP_NAME.service"
SUDOERS_FILE="/etc/sudoers.d/$APP_NAME"
LOG_FILE="/var/log/${APP_NAME}-install.log"

echo "Starting $APP_NAME install..." | tee -a "$LOG_FILE"

# 1. Ensure node is installed
echo "Installing Node.js..." | tee -a "$LOG_FILE"
apt update -y >> "$LOG_FILE" 2>&1
apt install -y nodejs npm >> "$LOG_FILE" 2>&1

# 2. Install node modules if package.json exists
if [ -f "$NODE_APP_PATH/package.json" ]; then
    echo "Installing Node.js dependencies..." | tee -a "$LOG_FILE"
    cd "$NODE_APP_PATH"
    npm install --omit=dev >> "$LOG_FILE" 2>&1
fi

# 3. Create systemd service
echo "Creating systemd service at $SERVICE_FILE..." | tee -a "$LOG_FILE"

cat <<EOF > "$SERVICE_FILE"
[Unit]
Description=SCADA Node.js Backend Service
After=network.target

[Service]
ExecStart=$NODE_EXEC --security-revert=CVE-2023-46809 $NODE_APP_PATH/app.js
WorkingDirectory=$NODE_APP_PATH
Restart=always
User=$USER
Environment=NODE_ENV=production

[Install]
WantedBy=multi-user.target
EOF

# 4. Set correct permissions
chmod 644 "$SERVICE_FILE"

# 5. Reload systemd and enable service
echo "Reloading systemd daemon and enabling $APP_NAME..." | tee -a "$LOG_FILE"
systemctl daemon-reexec
systemctl enable "$APP_NAME"
systemctl restart "$APP_NAME"

# 6. Setup sudo permissions for CI4 backend
echo "Configuring sudo permissions for www-data..." | tee -a "$LOG_FILE"

SUDOERS_FILE="/etc/sudoers.d/scada-node"
cat <<EOF > "$SUDOERS_FILE"
Defaults:www-data !requiretty
Defaults:www-data !env_reset
Defaults:www-data secure_path="/usr/sbin:/usr/bin:/sbin:/bin"

www-data ALL=(ALL) NOPASSWD: /usr/bin/journalctl *
www-data ALL=(ALL) NOPASSWD: /bin/systemctl *
EOF

chmod 440 "$SUDOERS_FILE"

# Confirm sudoers file is valid
visudo -cf "$SUDOERS_FILE" || { echo "Invalid sudoers config, aborting."; exit 1; }


# 7. Verify service status
echo "Verifying service status..." | tee -a "$LOG_FILE"
systemctl is-active "$APP_NAME" | tee -a "$LOG_FILE"

echo "Installation complete. Logs saved to $LOG_FILE"
