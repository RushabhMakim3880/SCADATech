# HPT Innovance 6-Head CNC Angle Punching, Marking & Shearing SCADA System

> **Client:** Hydro Power Tech Engineering (HPT) — Ravki Industrial Area, Rajkot, Gujarat  
> **Application:** Heavy Industrial SCADA & Touchscreen Kiosk HMI for CNC Transmission Tower Angle Steel Processing Lines (HPT-HA Series: HA-122, HA-163, HA-203, HA-253)

---

## 📋 System Overview

The **HPT Innovance SCADA System** is an enterprise-grade, touchscreen-optimized industrial control and telemetry platform. It is engineered specifically for automated 6-head CNC angle punching, character stamping, and single-cut hydraulic shearing lines powered by **Innovance H3U / H5U PLCs** and **IS620N / IS810N Servo Systems**.

It replaces legacy desktop software with a modern, high-speed, 20Hz reactive TypeScript & React architecture matching global structural steel benchmarks (**Ficep Polaris & Steel Projects**, **Voortman VACAM**, and **Peddinghaus Raptor**).

```mermaid
graph TD
    subgraph Shopfloor Hardware [Hydro Power Tech Machine Hardware]
        PLC["Innovance H3U / H5U PLC (EtherCAT / Modbus TCP)"]
        SERVO["Carriage Feed Servo (Princher X Axis)"]
        HPU["Main Hydraulic Power Unit (145 bar)"]
        HEADS["6x Punch Cylinders (DA1-DA3, DB1-DB3)"]
        MARK["Hydraulic Stamping Cassette"]
        SHEAR["Hydraulic Shear Unit"]
    end

    subgraph Server Gateway [Node.js Fastify & WebSocket Server]
        MODBUS["Modbus TCP / Serial PLC Driver"]
        SIM["High-Fidelity Real-time PLC Simulator"]
        TAGS["20Hz Reactive Tag Engine (UI & System Tags)"]
        WS["Bidirectional WebSocket Gateway"]
        DB["SQLite & JSON Recipe Persistence"]
    end

    subgraph Client HMI [React 19 + TypeScript + Tailwind CSS]
        PROD["Manage Production & 2D CAD Blueprint"]
        NEST["Linear Multibar Nesting & Scrap Minimizer"]
        RECIPE["Recipe Master & DSTV / NC1 CAD Importer"]
        RULES["IS 802 / ASTM Tower Rule Validator"]
        OEE["Shift Production & Metric Tonnage Analytics"]
        IO["32-Channel PLC I/O Diagnostics Matrix"]
        WEAR["Tooling Life & Stroke Wear Monitor"]
    end

    PLC <--> MODBUS
    SIM <--> TAGS
    MODBUS <--> TAGS
    TAGS <--> WS
    WS <--> Client HMI
    DB <--> Server Gateway
```

---

## ✨ Core Key Features

### 1. 📐 2D Angle Bar Visual Blueprint & Carriage Laser Track
* Unfolded 2D orthographic steel CAD visualizer displaying Flange A (top) and Flange B (bottom) along the central **Bend Heel Datum Line**.
* Vivid color-coded tooling stations:
  * 🔵 **Flange A Punch Dies (DA1–DA3)**: Glowing Cyan rings ($\varnothing 14 - \varnothing 32\text{mm}$) with center crosshairs.
  * 🟢 **Flange B Punch Dies (DB1–DB3)**: Glowing Emerald Green rings.
  * 🟡 **Marking Stamp Cassette**: Amber identification tag box.
  * 🔴 **Hydraulic Cutter**: Solid neon red shear cut line.
* Proportional flange scaling and real-time carriage laser feed line (`X: 0.00 mm`).
* Full CAD toolbar: *Pan, Zoom In/Out, Fit Viewport, Flip Flanges A/B, and Dimension Toggles*.

### 2. 📁 Tekla DSTV / NC1 CAD File Importer
* Drag-and-drop parser for standard **Tekla Structures, SDS/2, and Bocad `.nc1` structural steel files**.
* Automatically parses `ST` (Header), `BO` (Holes), `SI` (Stamp text), and `IK` (Cut blocks), converting them into an active recipe with 1-click.

### 3. 🛡️ Transmission Tower Design Rule Checker (IS 802 / ASTM A394)
* Automated engineering rule verification preventing bolt tear-out under high electrical line tension:
  * **Min Edge Distance**: $e_{min} \ge 1.5 \times \text{die diameter}$ from angle toe tip.
  * **Min Heel Gauge**: $g_{min} \ge 1.5 \times \text{die diameter} + \text{thickness}$ from bend heel fold.
  * **Min Pitch Spacing**: $p_{min} \ge 2.5 \times \text{die diameter}$ between consecutive holes.
* Configurable rule multipliers for specific tower client specifications (KEC, Kalpataru, L&T).

### 4. 🧩 Multibar Linear Nesting & Scrap Minimizer
* Linear First-Fit Decreasing (FFD) multibar nesting algorithm.
* Automatically packs batch work orders into standard commercial raw stock bars ($6\text{m}, 9\text{m}, 12\text{m}$) with **$<1.2\%$ remnant scrap**.
* Configurable shear kerf blade loss and carriage gripper clamp dead-zones.

### 5. ⚡ PLC Hardware I/O Signal Diagnostics Matrix
* Live visual status for **32 Digital Inputs ($X0–X37$)** and **32 Digital Outputs ($Y0–Y37$)** for Innovance H3U/H5U PLCs.
* Instant visual LED feedback for limit switches, proximity sensors, hydraulic pressure switches, and solenoid valves.
* **Field Force Mode**: Manual override triggers for field commissioning and maintenance without opening the electrical cabinet.

### 6. 📊 Shift Production & Metric Tonnage OEE Analytics
* **Processed Metric Tonnage Calculator**: $\text{Tons} = \sum (\text{Length [m]} \times \text{Weight/m [kg]}) / 1000$.
* Shift tracking (Shift A, Shift B, Shift C) with runtime vs idle vs fault downtime breakdown.
* Full OEE Score (Availability $\times$ Performance $\times$ Quality).
* 1-click **Export Shift Production CSV** and **Printable Report**.

### 7. ⚙️ Tooling Life & Stroke Wear Monitor
* Stroke telemetry tracking for all 6 punch dies, marking cassette, and shear blade.
* Visual wear progress bars with **Regrind Due Alerts** at $>85\%$ wear.
* 1-click **Reset Counter after Regrind/Change**.

### 8. 📄 Workshop Production Job Card & Cutting Sheet
* Generates formatted, printable shopfloor work orders with tower item details, material grade (IS 2062 E250 / E350), tooling die schedule, and sign-off blocks.

---

## 🏗️ Monorepo Workspace Structure

```
hptinnovanceanglepunchinghead6.test/
├── apps/
│   ├── client/                  # React 19 + Vite + Tailwind CSS + Lucide Icons
│   │   ├── src/
│   │   │   ├── components/      # UI components (Canvas CAD, DataTables, Keypads, Modals)
│   │   │   ├── views/           # SCADA Views (LiveProduction, RecipeMaster, OEE, IO, etc.)
│   │   │   ├── stores/          # Zustand Reactive PLC & Tag Stores
│   │   │   └── services/        # WebSocket client & API drivers
│   │   └── package.json
│   ├── server/                  # Fastify + WebSocket Server + Modbus TCP & Simulator Engine
│   │   ├── src/
│   │   │   ├── plc/             # Innovance Modbus TCP driver & 20Hz Simulator
│   │   │   ├── routes/          # REST API (Recipes, Production, Tags, Alarms, Logs)
│   │   │   └── database/        # SQLite Persistence Engine
│   │   └── package.json
│   └── desktop/                 # Electron Kiosk Desktop Wrapper
│       ├── src/                 # Main process, kiosk window manager, hardware serial bridge
│       └── package.json
├── packages/
│   └── shared/                  # Shared TypeScript models, Tag interfaces, Recipe schemas
├── docs/                        # Architecture guides & Operator manual
└── package.json                 # Root monorepo workspace configuration
```

---

## 🚀 Quick Start Guide

### Prerequisites
* **Node.js**: v20.x or higher
* **npm**: v10.x or higher

### Installation
```bash
# Clone the repository
git clone https://github.com/RushabhMakim3880/SCADATech.git
cd SCADATech

# Install all monorepo dependencies
npm install
```

### Running in Development Mode
To launch the complete SCADA suite (Client UI + WebSocket Server + Real-time PLC Simulator):
```bash
npm run dev
```
* **Client HMI**: [`http://localhost:3000`](http://localhost:3000)
* **REST & WebSocket API**: [`http://localhost:5000`](http://localhost:5000)

### Building for Production
```bash
npm run build
```

---

## 🔌 PLC Communication Configuration

To connect to a physical **Innovance H3U / H5U PLC**:
1. Open `apps/server/src/config/plcConfig.json` (or configure via **PLC & Ui Tag Master** view in the UI).
2. Set the connection parameters:
   * **Protocol**: `Modbus TCP` (Default Port: `502`) or `EtherCAT / Serial RTU`
   * **PLC IP Address**: `192.168.1.10`
   * **Polling Scan Rate**: `50 ms` ($20\text{ Hz}$)
   * **Carriage Feed Axis Address**: Register `D1000` (Double Word 32-bit Float)
   * **Firing Solenoids**: Registers `M100 - M115`

---

## 📜 Compliance & Standards
* **IS 802**: Indian Standard for Use of Structural Steel in Overhead Transmission Line Towers.
* **IS 2062**: Hot Rolled Medium and High Tensile Structural Steel specifications.
* **ASTM A394**: Standard Specification for Steel Transmission Tower Bolts and Shear Tolerances.

---

## 👥 Authors & Credits
* **Developed for**: Hydro Power Tech Engineering (HPT), Rajkot, Gujarat
* **Engineering & SCADA Development**: Mindstien Technologies / Rushabh Makim
