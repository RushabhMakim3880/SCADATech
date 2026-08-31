# Installation, Setup & Deployment Guide

---

## 1. Environment & Prerequisites

* **Operating System**: Ubuntu 22.04 LTS / 24.04 LTS (Industrial PC recommended) or Windows Industrial OS.
* **PHP Runtime**: PHP 8.1 or higher with extensions: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `gd`.
* **Database**: MySQL 8.0+ or MariaDB 10.6+.
* **Node.js**: Node.js v18.x or v20.x LTS with NPM.
* **Web Server**: Nginx or Apache (configured with document root pointing to `public/`).

---

## 2. Web Application Setup (CodeIgniter 4)

### 2.1 Configuration
1. Copy the environment template:
   ```bash
   cp env.template .env
   ```
2. Edit `.env` and set production environment variables:
   ```ini
   CI_ENVIRONMENT = production
   app.baseURL = 'http://192.168.1.10/'
   
   database.default.hostname = localhost
   database.default.database = hpt_innovance_anglepunch
   database.default.username = db_user
   database.default.password = db_secure_password
   database.default.DBDriver = MySQLi
   ```

### 2.2 Database Migrations
Run the CodeIgniter Spark CLI to execute all schema migrations:
```bash
php spark migrate
```

### 2.3 File Permissions
Ensure the web server has write access to the runtime folders:
```bash
sudo chown -R www-data:www-data writable/ public/uploads/
sudo chmod -R 775 writable/ public/uploads/
```

---

## 3. PLC Gateway Daemon Setup (`nodeOpMaster`)

### 3.1 Install Dependencies
```bash
cd nodeOpMaster
npm install --production
```

### 3.2 Systemd Service Configuration
Create `/etc/systemd/system/scada-node.service`:
```ini
[Unit]
Description=HPT SCADA Node.js PLC Gateway Daemon
After=network.target mysql.service

[Service]
Type=simple
User=root
WorkingDirectory=/var/www/hptinnovanceanglepunchinghead6.test/nodeOpMaster
ExecStart=/usr/bin/node app.js
Restart=always
RestartSec=5
Environment=NODE_ENV=production

[Install]
WantedBy=multi-user.target
```

Enable and start the service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable scada-node.service
sudo systemctl start scada-node.service
```

### 3.3 Passwordless Sudo Permissions for Web Server
To allow the PHP HMI to manage the background service, add the following to `/etc/sudoers.d/scada-node`:
```bash
www-data ALL=(ALL) NOPASSWD: /bin/systemctl start scada-node.service
www-data ALL=(ALL) NOPASSWD: /bin/systemctl stop scada-node.service
www-data ALL=(ALL) NOPASSWD: /bin/systemctl restart scada-node.service
www-data ALL=(ALL) NOPASSWD: /bin/systemctl is-active scada-node.service
www-data ALL=(ALL) NOPASSWD: /bin/journalctl -u scada-node.service *
```

---

## 4. Industrial Network Configuration

Ensure the Industrial IPC network interface is assigned a static IP in the same subnet as the Innovance PLC:
* **IPC Static IP**: `192.168.1.10`
* **Subnet Mask**: `255.255.255.0`
* **Innovance PLC IP**: `192.168.1.100` (Default Port: `4840`)

Test connectivity:
```bash
ping 192.168.1.100
nc -zv 192.168.1.100 4840
```

---

## 5. Ubuntu Industrial Hardening Scripts

The repository includes automation scripts for dedicated kiosk touchscreens:
* **`ubuntuBranding.sh`**: Customizes OEM splash screens and system branding.
* **`ubuntuDisableUpdates.sh`**: Disables disruptive automated OS restarts and background update locks during machine production shifts.

---

## 6. Troubleshooting Matrix

| Issue | Potential Cause | Resolution |
| :--- | :--- | :--- |
| **"PLC is not connected"** | PLC power off, Ethernet unplugged, or wrong IP/Port | Check network cable, ping `192.168.1.100`, verify `plcMaster` table. |
| **WebSocket Connection Refused (:3001)** | `scada-node.service` is inactive or crashed | Check logs with `sudo journalctl -u scada-node.service -n 50`. |
| **Tag Write Rejected with 400 Error** | Value outside `minValue` or `maxValue` | Check `uiTagMaster` min/max safety limits for the targeted tag. |
| **HMI Screen shows stale DRO values** | Continuous read polling loop is paused | Ensure continuous tags are assigned in `plcTagMaster` and re-init PLC. |
