# ☸️ 🖧 MAchine & Plc Master
------

## Tablename: `machineMaster` (Normal crud operation)

```sql
machineId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
machineCode, VARCHAR(50) -- Unique code like "MACH01"
machineName, VARCHAR(100) -- e.g., "Hydraulic Puncher A"
machineType VARCHAR(100) -- e.g., "punching", "cnc", "drill"
location VARCHAR(100) -- e.g., "Plant 1 - Line 3"
headCount INT (11)
barMaxLength FLOAT -- in mm or inch
barUom VARCHAR(10) -- mm/inch
description TEXT
isActive, BOOLEAN
createdAt, DATETIME NULL
updatedAt, DATETIME NULL

```

## Tablename: `machineOperationConfig` (Normal crud operation)

```sql
operationConfigId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
machineId, INT  FK to machineMaster
operationCode VARCHAR(50) -- e.g., PUNCH_A, DRILL_TOOL_1, CUT_B
operationType VARCHAR(50) -- e.g., punch, cut, drill, move, clamp
operationLabel VARCHAR(100) -- e.g., "Round Hole Punch", shown in UI
positionX FLOAT NULL -- optional, if fixed tool on axis
positionY FLOAT NULL -- optional
isMovableHead TINYINT -- 1=yes, this tool head moves dynamically
plcTriggerTag  FK to plcTagMaster.tagId
plcAckTag  FK to plcTagMaster.tagId
plcAdditionalData JSON NULL -- e.g., toolId, speed, pressure
description TEXT
createdAt, DATETIME NULL
updatedAt, DATETIME NULL
```

## Tablename: `plcMaster` (Normal crud operation)

```sql
plcId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
machineId, INT  FK to machineMaster
plcName VARCHAR -- e.g., "Main PLC"
protocol ENUM('modbus-tcp', 'opc-ua', 'mqtt', 'custom')
ipAddress VARCHAR
port INT (11)
modbusDeviceId INT (11) -- for modbus
description TEXT
status TINYINT -- 1=active, 0=inactive
createdAt, DATETIME NULL
updatedAt, DATETIME NULL
```
## Tablename: `plcTagGroupMaster` (Normal crud operation)

```sql
tagGroupId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
plcId, INT  FK to plcMaster
groupName VARCHAR -- e.g., "Punch Control", "Alarm Status"
description TEXT
createdAt, DATETIME NULL
updatedAt, DATETIME NULL
```


## Tablename: `plcTagMaster` (db only, will plan for ui later)

```sql
tagId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
plcId, INT  FK to plcMaster
tagGroupId INT NULL (FK to plcTagGrupMaster)
tagName VARCHAR -- e.g., "punchTrigger", "headXPos"
tagAddress VARCHAR -- address like "1000" or "HR40001"
dataType ENUM('int16','int32','float32','bool','string')
registerType ENUM('coil','holding','input','discrete','variable') -- protocol-specific
readWrite ENUM('read','write','readwrite')
scaleFactor FLOAT DEFAULT 1.0
offset FLOAT DEFAULT 0.0
unit VARCHAR(20) NULL -- e.g., mm, bar, etc.
description TEXT
isActive TINYINT DEFAULT 1
createdAt, DATETIME NULL
updatedAt, DATETIME NULL
```

## Tablename: `plcRuntimeLog` (db only)

```sql
plcRuntimeLogId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
plcId, INT  FK to plcMaster
tagId, INT FK to plcTagMaster
operationType ENUM('read','write')
value VARCHAR(100)
responseTimeMs INT (11)
status ENUM('success','timeout','error')
errorMessage TEXT NULL
triggeredBy VARCHAR(50) -- 'nodejs-engine', 'manual-ui', etc.
createdAt DATETIME
```