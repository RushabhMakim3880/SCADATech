# 🏷️  Ui Tag Master
------

## Tablename: `uiTagMaster` (Normal crud operation)

```sql
uiTagId, PK INT UNSIGNED 
tenantId, Fk to tenantMaster, INT 11 UNSIGNED
serialNo	int(10)		UNSIGNED	No	0
tagId (FK plcTagMaster)
tagGroupId (FK plcTagGroupMaster)
tagName (varchar 100)
isActive, BOOLEAN
updatedAt, DATETIME NULL
updatedBy, FK TO userMaster, INT(11) unsigned null
createdAt, DATETIME NULL
createdBy, FK to userMaster, INT(11) unsigned null

```

