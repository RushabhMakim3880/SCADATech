export type UserRole = 'SUPER_ADMIN' | 'ADMIN' | 'OPERATOR';

export interface User {
  id: string;
  username: string;
  name: string;
  role: UserRole;
  pinCode?: string | null;
  permissions: string[];
  isActive: boolean;
  createdAt?: string | Date;
  updatedAt?: string | Date;
}

export interface PermissionDefinition {
  key: string;
  label: string;
  category: 'NAVIGATION' | 'MACHINE_CONTROL' | 'RECIPES' | 'MAINTENANCE' | 'SYSTEM';
  description: string;
}

export const SYSTEM_PERMISSIONS: PermissionDefinition[] = [
  // Navigation Menus
  { key: 'menu:dashboard', label: 'View SCADA Home Dashboard', category: 'NAVIGATION', description: 'Access central SCADA square-button launcher' },
  { key: 'menu:production', label: 'View Live Production & 2D CAD', category: 'NAVIGATION', description: 'Access live angle bar visualization and work orders' },
  { key: 'menu:manual', label: 'View Manual Controls', category: 'NAVIGATION', description: 'Access manual carriage jog and cylinder trigger panel' },
  { key: 'menu:recipes', label: 'View Item Recipe Master', category: 'NAVIGATION', description: 'View angle steel recipes and hole patterns' },
  { key: 'menu:alignment', label: 'View Program Alignment & Nesting', category: 'NAVIGATION', description: 'Access linear nesting and scrap minimizer' },
  { key: 'menu:oee', label: 'View Shift & OEE Telemetry', category: 'NAVIGATION', description: 'Access shift tonnage, runtime and OEE statistics' },
  { key: 'menu:wear', label: 'View Tooling Wear & Life', category: 'NAVIGATION', description: 'Monitor stroke wear counts for all 6 punch dies and shear' },
  { key: 'menu:io', label: 'View PLC I/O Diagnostics', category: 'NAVIGATION', description: 'View real-time 32 digital inputs (X) and 32 outputs (Y)' },
  { key: 'menu:setup', label: 'View Machine Setup Settings', category: 'NAVIGATION', description: 'View physical machine limits, head offsets and dimensions' },
  { key: 'menu:tags', label: 'View PLC & UI Tag Master', category: 'NAVIGATION', description: 'Inspect Modbus/OPC-UA communication tag registers' },
  { key: 'menu:alarms', label: 'View Alarm Config & Logs', category: 'NAVIGATION', description: 'View active alarms and alarm history log' },
  { key: 'menu:users', label: 'View User & Permission Management', category: 'NAVIGATION', description: 'Access user list and operator permission matrix' },
  { key: 'menu:config', label: 'View Super Admin Menu Config', category: 'NAVIGATION', description: 'Customize SCADA square button layout and visibility (With US only)' },

  // Operational Machine Actions
  { key: 'action:start_production', label: 'Execute Auto Production Cycles', category: 'MACHINE_CONTROL', description: 'Start, pause and resume automated angle processing cycles' },
  { key: 'action:jog_axis', label: 'Manual Servo Feed Jogging', category: 'MACHINE_CONTROL', description: 'Jog carriage forward and reverse at slow/fast speeds' },
  { key: 'action:fire_heads', label: 'Manual Head Cylinder Fire', category: 'MACHINE_CONTROL', description: 'Trigger punch cylinders DA1-DA3, DB1-DB3, marker and shear' },
  { key: 'action:toggle_valves', label: 'Hydraulic Clamp & Valve Control', category: 'MACHINE_CONTROL', description: 'Operate infeed, carriage, outfeed clamps and HPU pump' },

  // Recipes & Engineering
  { key: 'action:edit_recipe', label: 'Create & Modify Item Recipes', category: 'RECIPES', description: 'Add, update or delete punch coordinates and steel profiles' },
  { key: 'action:import_dstv', label: 'Import Tekla / NC1 CAD Files', category: 'RECIPES', description: 'Parse DSTV NC1 files directly into active production recipes' },

  // Maintenance & Diagnostics
  { key: 'action:reset_tool_wear', label: 'Reset Tooling Stroke Life', category: 'MAINTENANCE', description: 'Reset tool stroke wear counters after die replacement or regrind' },
  { key: 'action:force_io', label: 'PLC Field Force Override Mode', category: 'MAINTENANCE', description: 'Force manual override of physical PLC hardware inputs and outputs' },

  // Administration
  { key: 'action:manage_users', label: 'Manage Users & Operator Permissions', category: 'SYSTEM', description: 'Create operators, update PINs and configure permission matrices' },
  { key: 'action:super_menu_config', label: 'Super Admin Menu Configuration', category: 'SYSTEM', description: 'Master menu reconfiguration and factory default controls (With US only)' },
];

export interface MenuConfigItem {
  id?: string;
  menuKey: string;
  label: string;
  icon: string;
  description?: string | null;
  order: number;
  isEnabled: boolean;
  roles: UserRole[];
  category: 'OPERATIONAL' | 'ENGINEERING' | 'ADMINISTRATION';
  badgeText?: string | null;
  badgeColor?: string | null;
}
