# PLC SCADA System - Copilot Instructions

## Architecture Overview

This is a multi-tenant industrial PLC SCADA system built with **CodeIgniter 4 (PHP)** backend and **Node.js** real-time services. The architecture consists of:

- **PHP Backend**: RESTful APIs, multi-tenant database management, user authentication, CRUD operations
- **Node.js OpMaster**: Real-time OPC-UA client, WebSocket server, alarm management, continuous tag polling
- **Modular Structure**: Separate Backend/Frontend modules for each feature domain (PlcMaster, AlarmConfig, etc.)

## Key Components

### Multi-Tenant Architecture
- Every table has `tenantId` field (except global masters like `countryMaster`)
- Tenant detection via Auth user, subdomain, or fallback to default (ID=1)
- Dynamic configuration loading via `SettingManager::getAllResolvedSettings()`

### Module Structure Pattern
```
Modules/
├── Backend/PlcMaster/     # API controllers, models
├── Frontend/PlcMaster/    # Views, client-side logic
```

Each module follows: `Controllers/`, `Models/`, `Database/`, `Config/`

### Node.js Real-Time Layer
- **OPC-UA Client**: Industrial protocol communication (`opcuaClient.js`)
- **WebSocket Server**: Single-client real-time updates (`wsServer.js`)
- **PLC Manager**: Tag polling, read/write operations (`plcManager.js`)
- **Alarm Manager**: Real-time alarm processing (`alarmManager.js`)

## Essential Patterns

### API Controllers
- Extend `ApiBaseController` - provides auth, permissions, input handling
- Use `UserPermissionLib::userCanDo("ModuleName", 'action')` for authorization
- Input via `$this->getInputData()` - handles JSON/multipart forms
- Response via `ResponseTrait` methods: `respond()`, `failForbidden()`, etc.

### Database Conventions (Follow `docs/SOP.md`)
- Primary keys: `INT UNSIGNED` (use `BIGINT` only for massive IoT data)
- Financial: `DECIMAL(18,2)`, Quantities: `DECIMAL(10,2)`
- Booleans: `TINYINT(1)` with explicit `DEFAULT 0` or `1`
- Meta fields: `createdAt`, `createdBy`, `updatedAt`, `updatedBy` in every table
- Soft delete: `isDeleted TINYINT(1) DEFAULT 0` for transactional tables
- Active flags: `isActive TINYINT(1) DEFAULT 1` for master/config tables

### CRUD Generation System
- Use `php spark crud:generate` command with `autoCrudTemplates/`
- Placeholders: `{{MODULE_NAME}}`, `{{ITEM_NAME}}`, `{{TABLE_NAME}}`, etc.
- Auto-generates: Controllers, Models, Views, Routes, Permissions

### Node.js Communication
- Internal API calls via `nodejsApi()` helper with `X-Internal-Token` header
- WebSocket authentication via JWT cookies
- Real-time data broadcast to single WebSocket client
- Background service management via `isBackground: true` in terminal commands

## Common Workflows

### Adding New Module
1. Define database schema following migration SOP (`docs/migrationSOP.md`)
2. Run `php spark crud:generate ModuleName` 
3. Update `MenuConfig.json` for navigation
4. Configure permissions in generated code
5. Test via Postman/API documentation

### Node.js Service Development
- Services auto-start via `app.js` → loads config → initializes components
- Add new services to `app.js` initialization sequence
- Use `config.js` for centralized configuration
- Implement error handling with backend notification via `sendToBackend()`

### Development Commands
```bash
# Start PHP development server
php spark serve

# Start Node.js services (with CVE revert)
cd nodeOpMaster && npm start

# Generate CRUD module
php spark crud:generate ModuleName

# Database migrations
php spark migrate
```

## Key Files to Reference
- `app/Controllers/ApiBaseController.php` - Base API patterns
- `app/Helpers/project_helper.php` - Custom utilities like `nodejsApi()`
- `app/Libraries/Tenant.php` - Multi-tenant resolution logic
- `nodeOpMaster/app.js` - Service initialization sequence
- `docs/SOP.md` - Database field standards
- `MenuConfig.json` - Navigation structure

## Security & Permissions
- JWT-based authentication for both PHP and Node.js
- Permission system: `UserPermissionLib` with module.action granularity
- Tenant isolation at database level
- Internal service communication via secure tokens