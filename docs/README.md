# HPT Innovance 6-Head CNC Angle Iron Punching, Marking & Shearing System
## Comprehensive Technical & Operational Documentation

---

### 1. Executive Summary
The **HPT Innovance Angle Punching System** is an industrial SCADA (Supervisory Control and Data Acquisition), MES (Manufacturing Execution System), and CNC HMI (Human-Machine Interface) solution designed specifically for heavy-duty angle iron processing in structural steel fabrication, transmission tower production, telecommunications masts, and pre-engineered buildings (PEB).

The system interfaces with an **Innovance Industrial PLC** controlling high-precision servo infeed drives, hydraulic punching stations, pneumatic/hydraulic marking cassettes, and hydraulic shearing blades.

```
       [ CAD / DSTV Files (.DAT) ]
                   │
                   ▼
       [ Recipe Parser & Validation ]
                   │
                   ▼
     [ Production Planning & Nesting ]
 (Multi-item grouping & Scrap Optimization)
                   │
                   ▼
     [ Real-Time CNC Execution Engine ]
                   │
  ┌────────────────┴────────────────┐
  ▼                                 ▼
[ CodeIgniter 4 Backend ]    [ NodeOpMaster Daemon ]
(Data, Config, Reporting)    (OPC-UA Client & WebSocket)
  │                                 │
  └────────────────┬────────────────┘
                   ▼
        [ Innovance Industrial PLC ]
 (6 Punch Heads + 4 Marking Cassettes + Cutter + Servo)
```

---

### 2. Core Machine Configuration
The machine physical setup consists of:
* **Side A Punching Heads**: 3 hydraulic punching cylinders (`DA1`, `DA2`, `DA3`) with distinct tool die sizes (e.g. Ø16mm, Ø20mm, Ø25mm).
* **Side B Punching Heads**: 3 hydraulic punching cylinders (`DB1`, `DB2`, `DB3`) corresponding to the perpendicular flange of the angle bar.
* **Marking Unit**: 4 multi-character alphanumeric stamping cassettes for part number / mark identification.
* **Shearing / Cutting Unit**: Heavy-duty hydraulic cutting blade for piece separation at exact lengths.
* **Infeed Princher / Servo Carriage**: High-speed servo motor drive with mechanical clamping grippers for longitudinal positioning ($X$-axis).

---

### 3. Key Capabilities & Features

| Capability | Description |
| :--- | :--- |
| **DSTV / DAT Parsing** | Direct ingestion of CNC cutting files, extracting flange sizes, thickness, punches, marks, and cut-off points. |
| **Safety Interlock Calculation** | Automatic calculation of safe root clearance ($Y_{\text{safe}}$) to prevent tool collision with the angle heel. |
| **Program Alignment & Nesting** | Dynamic nesting of multiple parts onto commercial raw bar lengths (6m, 9m, 12m) with scrap optimization. |
| **Real-Time OPC-UA & WebSockets** | Sub-second bidirectional telemetry with Innovance PLC, live Digital Readouts (DRO), and one-click controls. |
| **Declarative UI Tag Binding** | In-browser tag mapping supporting momentary buttons, maintained switches, gauges, and safety interlocks. |
| **Tool Life & Die Management** | Continuous hit counting per punch head/die with wear alerts and preventative maintenance scheduling. |
| **OEE & Downtime Tracking** | Automated idle state detection (monitoring hydraulic motors and valves) with operator pause reason prompts. |

---

### 4. Technical Architecture Stack

* **Backend Web Framework**: PHP 8.1+ with **CodeIgniter 4 (CI4)** (PSR-4 Modular Architecture under `Modules/Backend` and `Modules/Frontend`).
* **Database**: MySQL / MariaDB with foreign keys, transactional integrity, and database migrations.
* **PLC Gateway Daemon (`nodeOpMaster`)**: Node.js microservice utilizing `node-opcua`, `ws` (WebSockets), `express` REST API, and `async-mutex`.
* **Frontend HMI Layer**: Responsive SCADA interface (HTML5, Bootstrap, Vanilla JavaScript, CSS3 animations) designed for industrial touchscreens.
* **Operating System Target**: Ubuntu Linux LTS / Windows Industrial IPC with systemd service orchestration (`scada-node.service`).

---

### 5. Documentation Navigation Sitemap

Explore the detailed technical documentation sections:

1. **[System Architecture](architecture/systemArchitecture.md)** - End-to-end data flow, protocol specifications, threading, and daemon management.
2. **[Machine & PLC Configuration](modules/machineAndPlc.md)** - Machine master, head offsets, tool tooling configuration, and PLC communication parameters.
3. **[UI Tag Master & Data Binding](modules/uiTagMaster.md)** - Declarative data attributes, control types, safety interlocks, and write audit logging.
4. **[Recipe & DAT File Parser](modules/recipeAndDatParser.md)** - Parsing syntax, coordinate normalization, and $Y_{\text{safe}}$ root collision avoidance formulas.
5. **[Program Alignment & Nesting Engine](modules/programAlignmentAndNesting.md)** - Multi-job bar nesting, Lead vs. Princher Scrap balance, and rotation logic.
6. **[Operator HMI & CNC Control](modules/operatorHmiAndControl.md)** - Auto cycle, manual jogging, homing routines, pause/resume, and power recovery.
7. **[Alarms & Safety System](modules/alarmsAndDiagnostics.md)** - Software threshold alarms (LOLO, LO, HI, HIHI), PLC alarms, and audit logs.
8. **[Production Analytics, OEE & KPIs](modules/productionOeeAndKpi.md)** - Cycle timers, automated idle detection, downtime reasons, and theoretical weight formulas.
9. **[NodeOpMaster API & Protocols](api/nodeOpMasterApi.md)** - REST API endpoints, WebSocket event schemas, and daemon health checks.
10. **[Database Schema Reference](database/databaseSchema.md)** - Complete dictionary of all tables, columns, relations, and indexes.
11. **[Installation, Setup & Deployment](setup/installationAndDeployment.md)** - Environment configuration, systemd services, and troubleshooting.
