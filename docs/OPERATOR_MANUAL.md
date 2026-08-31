# Shopfloor Operator & Maintenance Manual

> **Machine Model:** HPT-HA Series (6-Head CNC Angle Line)  
> **Manufacturer:** Hydro Power Tech Engineering, Rajkot, Gujarat  
> **Controller:** Innovance H3U/H5U PLC & IS620N Servos

---

## 📖 Table of Contents
1. [Daily Machine Startup & Safety Interlocks](#1-daily-machine-startup--safety-interlocks)
2. [Loading & Creating Item Recipes](#2-loading--creating-item-recipes)
3. [Importing Tekla DSTV (.nc1) Files](#3-importing-tekla-dstv-nc1-files)
4. [Running IS 802 Tower Design Rule Verification](#4-running-is-802-tower-design-rule-verification)
5. [Automatic Production Execution](#5-automatic-production-execution)
6. [Manual Carriage Jogging & Tool Stroke Testing](#6-manual-carriage-jogging--tool-stroke-testing)
7. [Shift Tonnage Tracking & OEE Reports](#7-shift-tonnage-tracking--oee-reports)
8. [Tooling Wear Counter & Regrind Maintenance](#8-tooling-wear-counter--regrind-maintenance)
9. [PLC I/O Diagnostics & Fault-Finding](#9-plc-io-diagnostics--fault-finding)

---

## 1. Daily Machine Startup & Safety Interlocks
1. Turn on the main electrical isolator switch on the HPT electrical control cabinet.
2. Ensure the **Emergency Stop pushbutton** on the console is released.
3. Close the perimeter safety interlock doors around the 6 punch heads.
4. On the HMI top header, verify that the **PLC Status indicator** shows **`20Hz ONLINE`**.
5. Switch to **`Manual Operations`** and click **`MAIN HYD MOTOR`** to start the hydraulic power unit (HPU). Verify hydraulic pressure rises to **$140 - 145\text{ bar}$**.

---

## 2. Loading & Creating Item Recipes
1. Navigate to **`Item Recipe Master`** from the left sidebar.
2. To create a new recipe manually:
   * Click **`+ Add Item Recipe`**.
   * Enter **Item Code** (e.g. `TOWER-L15-01`), **Side A Width** ($75\text{mm}$), **Side B Width** ($75\text{mm}$), **Thickness** ($6\text{mm}$), and **Program Length** ($1500\text{mm}$).
   * Use the **Calculator Icon** to open the on-screen virtual keypad if operating with gloves.
   * Add punch hole steps by specifying **Side (A/B)**, **X Position (from datum)**, **Y Position (gauge)**, and **Die Tool Diameter** ($\varnothing 18\text{mm}$).
   * Click **`Save Recipe`**.

---

## 3. Importing Tekla DSTV (.nc1) Files
1. In **`Item Recipe Master`**, click the green **`Import DSTV (.nc1)`** button.
2. Drag and drop any standard `.nc1` structural steel file from Tekla, SDS/2, or Bocad into the upload box.
3. The visualizer will instantly parse all punch coordinates, stamping text, and shear cuts.
4. Click **`Load Imported Recipe into Editor`** and save!

---

## 4. Running IS 802 Tower Design Rule Verification
1. Open any recipe in **`Item Recipe Master`** (Edit mode).
2. Click the yellow **`Check IS 802 Rules`** button at the top right.
3. The system will automatically check:
   * **Minimum Edge Distance** from the angle toe tip ($e \ge 1.5 \times \varnothing$).
   * **Minimum Heel Gauge** from the bend fold ($g \ge 1.5 \times \varnothing + t$).
   * **Minimum Pitch Spacing** between adjacent holes ($p \ge 2.5 \times \varnothing$).
4. If any violations exist, they will be highlighted with the exact step number and required clearance dimensions.

---

## 5. Automatic Production Execution
1. Navigate to **`Manage Production`** from the sidebar.
2. Load the desired item recipe or nested production cycle.
3. Check the **`RAW STOCK INFEED TRACK`** to verify raw bar length ($6\text{m}, 9\text{m}, 12\text{m}$).
4. Click **`Start Auto Cycle`** (Green Button):
   * The servo carriage will grip the raw angle bar and advance to the first punch coordinate.
   * The allocated punch head ($DA1–DA3$ for Flange A, $DB1–DB3$ for Flange B) will stroke down.
   * The marking unit will stamp the piece tag.
   * The hydraulic shear will cut off the finished piece.
5. You can use **`Step-by-Step Mode`** to inspect the first piece before running full auto.

---

## 6. Manual Carriage Jogging & Tool Stroke Testing
1. Navigate to **`Manual Operations`**.
2. Select an incremental jog step ($0.1\text{mm}, 1\text{mm}, 10\text{mm}, 50\text{mm}, 100\text{mm}$).
3. Use **`Step + / Step -`** to jog the feed carriage.
4. To test a single punch cylinder:
   * Ensure the HPU motor is running.
   * Click any head button (**`DA1`, `DA2`, `DA3`, `DB1`, `DB2`, `DB3`, `Marking`, `Cutter`**) to execute a single test stroke.

---

## 7. Shift Tonnage Tracking & OEE Reports
1. Click **`Shift & OEE Telemetry`** from the sidebar.
2. Select the current working shift (**Shift A**, **Shift B**, or **Shift C**).
3. View real-time **Processed Metric Tonnage**, angle piece counts, machine runtime hours, and overall OEE score.
4. Click **`Export Shift CSV`** to download a spreadsheet report for factory management or click **`Print Report`** to print the shift summary.

---

## 8. Tooling Wear Counter & Regrind Maintenance
1. Click **`Tooling Wear & Life`** from the sidebar.
2. Inspect the stroke wear percentage for each punch station.
3. When a punch die reaches **$85\%$ wear** or requires regrinding/replacement:
   * Replace the physical punch & die set in the machine head.
   * Click **`Reset Counter After Regrind`** on the respective station card to reset the stroke count to zero.

---

## 9. PLC I/O Diagnostics & Fault-Finding
1. Click **`PLC I/O Diagnostics (X/Y)`** from the sidebar.
2. **Digital Inputs (X0–X37)**:
   * Green LED indicates a closed contact (e.g. carriage home sensor, clamp closed sensor, pressure switch OK).
   * Grey LED indicates an open contact.
3. **Digital Outputs (Y0–Y37)**:
   * Shows active solenoid valve signals and motor contactor relays.
4. **Field Force Mode**:
   * For authorized maintenance personnel to manually override output signals during maintenance testing.
