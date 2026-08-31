# NodeOpMaster API & Protocols Reference

---

## 1. Service Overview
The **NodeOpMaster** daemon exposes two network interfaces on the local host:
1. **REST API (Port 3000)**: Internal HTTP endpoints invoked by the CodeIgniter 4 backend for lifecycle management, tag writes, and tag synchronization.
2. **WebSocket Server (Port 3001)**: Real-time event streaming server broadcasting live PLC telemetry, digital readouts, and alarm triggers directly to client browsers.

---

## 2. REST API Endpoints (Port 3000)

### 2.1 Initialize PLC Connection
* **Endpoint**: `POST /api/initPlc`
* **Description**: Instructs the daemon to establish an OPC-UA session with the Innovance PLC.
* **Request Body**:
```json
{
  "plcHost": "192.168.1.100",
  "plcPort": 4840,
  "plcProtocol": "opc-ua"
}
```
* **Success Response (200 OK)**:
```json
{
  "success": true,
  "message": "PLC connected successfully",
  "endpoint": "opc.tcp://192.168.1.100:4840"
}
```

---

### 2.2 Synchronize / Browse Tags
* **Endpoint**: `GET /api/syncTags`
* **Description**: Recursively scans the PLC address space (`Application.GVL`) and returns all discovered variables with data types.
* **Success Response (200 OK)**:
```json
{
  "success": true,
  "tags": [
    {
      "tagName": "S_ACT_POS",
      "tagAddress": "ns=2;s=Application.GVL.S_ACT_POS",
      "dataType": "double"
    },
    {
      "tagName": "AUTO_ON_GO",
      "tagAddress": "ns=2;s=Application.GVL.AUTO_ON_GO",
      "dataType": "bool"
    }
  ]
}
```

---

### 2.3 Set Continuous Read Tags
* **Endpoint**: `POST /api/readTagsContinue`
* **Description**: Sets the active list of tag IDs and NodeIds to be polled in the continuous background read loop.
* **Request Body**:
```json
{
  "tags": {
    "101": "ns=2;s=Application.GVL.S_ACT_POS",
    "102": "ns=2;s=Application.GVL.Q_SV_LOW_PRESSURE",
    "103": "ns=2;s=Application.GVL.S_PRINCHER_AT_ZERO_1"
  }
}
```

---

### 2.4 Write Tags
* **Endpoint**: `POST /api/writeTags`
* **Description**: Atomically writes one or more values to PLC tags using `async-mutex` protection.
* **Request Body**:
```json
{
  "tags": {
    "ns=2;s=Application.GVL.AUTO_ON_GO": {
      "value": true,
      "dataType": "bool"
    },
    "ns=2;s=Application.GVL.S_TARGET_POS": {
      "value": 2450.5,
      "dataType": "double"
    }
  }
}
```
* **Success Response (200 OK)**:
```json
{
  "success": true,
  "message": "Tags written successfully"
}
```

---

### 2.5 Reload Alarm Configs
* **Endpoint**: `POST /api/reloadAlarms`
* **Description**: Dynamically updates the in-memory threshold monitoring table in `alarmManager.js` without restarting the daemon.

---

## 3. WebSocket Event Schemas (Port 3001)

### 3.1 Real-Time Telemetry Broadcast (`tagValues`)
Sent every 100-250ms with live values of all continuous read tags.
```json
{
  "type": "tagValues",
  "tags": {
    "101": 2450.50,
    "102": true,
    "103": 185.2
  }
}
```

### 3.2 PLC Connection Status (`plcStatus`)
```json
{
  "type": "plcStatus",
  "status": "connected",
  "message": "OPC UA connected to opc.tcp://192.168.1.100:4840"
}
```

### 3.3 Instant Alarm Notification (`alarm`)
```json
{
  "type": "alarm",
  "alarmId": "12",
  "level": "hihi",
  "value": 235.4,
  "active": true,
  "message": "Hydraulic Main Pressure Exceeded HIHI Safety Limit (230 Bar)"
}
```
