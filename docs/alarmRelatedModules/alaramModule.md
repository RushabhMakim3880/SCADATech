# ⏰ Alaram Modules
------

## Tablename: `alarmConfig` 

```sql
alarmId INT PK AUTO_INCREMENT
tenantId INT FK
serialNo INT
uiTagId	(FK to uiTagMaster)
loloTheresold (decimal)
loTheresold (decimal)
hiTheresold (decimal)
hihiTheresold (decimal)
isActive
updatedAt
updatedBy Datetime
createdAt
createdBy Datetime

```

### Notes:
Full Crud Operation 

## Tablename: `alarmLog`

```sql
stepId INT PK AUTO_INCREMENT
tenantId INT FK
serialNo INT
alarmId FK to alarmConfig
uiTagId	(FK to uiTagMaster)
alarmType enum (lo,lolo,hi,hihi)
triggerValue
triggerTime
resolveTime
isActive
```

### Notes:


Only manges screen add 
as it is whats aap msg to sir

---

