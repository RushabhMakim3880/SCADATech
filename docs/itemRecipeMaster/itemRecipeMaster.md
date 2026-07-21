# ☸️ 🖧 itemRecipeMaster
------

## Tablename: `itemRecipeMaster` (store master item recipe)

```sql
itemRecipeId INT PK AUTO_INCREMENT
tenantId INT FK
serialNo INT
itemCode VARCHAR(100)
description TEXT
sideAWidth decimal(10,2)
sideBWidth decimal(10,2)
sideAThickness decimal(10,2)
sideBThickness decimal(10,2)
material
programLength
isActive
updatedAt
updatedBy
createdAt DATETIME
createdBy

```

### Notes:
create migration files and full crud module with onetomany
date:27 aug whats msg changes to sir 
Remove item name replace to program
programLength (keep readonly, auto fill from cutting X position on save)


## Tablename: `itemRecipeSteps` (actual steps or list of operations for each recipe.)

```sql
stepId INT PK AUTO_INCREMENT
tenantId INT FK
serialNo INT
itemRecipeId INT FK
stepNo INT
operationConfigId INT -- FK to machineOperationConfig.operationConfigId
xPos decimal(10,2)
yPos decimal(10,2)
parameters JSON
```

### Notes:

Make crud module with onetomany (avoid using mobile specific views for manage screens in hptscada project)

in onetomany rows, have following fields.

operationConfigId (normal dropdown from machineOperationConfig)
stepNo
xPos
yPos
parameters (textarea)



---

