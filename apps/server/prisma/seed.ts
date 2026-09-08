import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Initializing Clean Production Database for HPT 6-Head Angle Processing Line...');

  // Clean out any legacy seed data
  await prisma.productionUnit.deleteMany();
  await prisma.productionBar.deleteMany();
  await prisma.programCycleOperation.deleteMany();
  await prisma.programCycle.deleteMany();
  await prisma.jobCardItem.deleteMany();
  await prisma.jobCard.deleteMany();
  await prisma.itemRecipeStep.deleteMany();
  await prisma.itemRecipe.deleteMany();
  await prisma.plcTag.deleteMany();
  await prisma.plcConfig.deleteMany();
  await prisma.machineSetup.deleteMany();
  await prisma.machineDetail.deleteMany();
  await prisma.machine.deleteMany();

  // 1. Create Default Super Admin, Admin & Operator Users
  await prisma.user.upsert({
    where: { username: 'superadmin' },
    update: {
      role: 'SUPER_ADMIN',
      pinCode: '7788',
      name: 'Super Admin (With US only)',
      permissions: JSON.stringify([
        'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
        'menu:oee', 'menu:wear', 'menu:io', 'menu:setup', 'menu:tags', 'menu:alarms',
        'menu:users', 'menu:config',
        'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves',
        'action:edit_recipe', 'action:import_dstv', 'action:reset_tool_wear', 'action:force_io',
        'action:manage_users', 'action:super_menu_config'
      ]),
    },
    create: {
      username: 'superadmin',
      name: 'Super Admin (With US only)',
      role: 'SUPER_ADMIN',
      pinCode: '7788',
      permissions: JSON.stringify([
        'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
        'menu:oee', 'menu:wear', 'menu:io', 'menu:setup', 'menu:tags', 'menu:alarms',
        'menu:users', 'menu:config',
        'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves',
        'action:edit_recipe', 'action:import_dstv', 'action:reset_tool_wear', 'action:force_io',
        'action:manage_users', 'action:super_menu_config'
      ]),
    },
  });

  await prisma.user.upsert({
    where: { username: 'admin' },
    update: {
      role: 'ADMIN',
      pinCode: '9999',
      name: 'Plant Administrator',
      permissions: JSON.stringify([
        'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
        'menu:oee', 'menu:wear', 'menu:io', 'menu:setup', 'menu:tags', 'menu:alarms',
        'menu:users',
        'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves',
        'action:edit_recipe', 'action:import_dstv', 'action:reset_tool_wear', 'action:manage_users'
      ]),
    },
    create: {
      username: 'admin',
      name: 'Plant Administrator',
      role: 'ADMIN',
      pinCode: '9999',
      permissions: JSON.stringify([
        'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
        'menu:oee', 'menu:wear', 'menu:io', 'menu:setup', 'menu:tags', 'menu:alarms',
        'menu:users',
        'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves',
        'action:edit_recipe', 'action:import_dstv', 'action:reset_tool_wear', 'action:manage_users'
      ]),
    },
  });

  await prisma.user.upsert({
    where: { username: 'operator' },
    update: {
      role: 'OPERATOR',
      pinCode: '1234',
      name: 'Shopfloor Line Operator',
      permissions: JSON.stringify([
        'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
        'menu:oee', 'menu:wear', 'menu:alarms',
        'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves'
      ]),
    },
    create: {
      username: 'operator',
      name: 'Shopfloor Line Operator',
      role: 'OPERATOR',
      pinCode: '1234',
      permissions: JSON.stringify([
        'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
        'menu:oee', 'menu:wear', 'menu:alarms',
        'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves'
      ]),
    },
  });

  // 2. Create Machine Configuration for HPT HA-Series CNC Angle Line
  await prisma.machine.create({
    data: {
      machineCode: 'HPT-HA-203',
      machineName: 'HPT 6-Head CNC Angle Punching, Marking & Shearing Line',
      machineType: 'HPT-HA Series',
      headCount: 6,
      minAngleSize: 40.0,
      maxAngleSize: 200.0,
      minThickness: 3.0,
      maxThickness: 20.0,
      maxBarLength: 12000.0,
      details: {
        create: [
          { headName: 'DA1', headType: 'PUNCHING', xPosition: 200.0, side: 'A', toolSize: 0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DA2', headType: 'PUNCHING', xPosition: 400.0, side: 'A', toolSize: 0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DA3', headType: 'PUNCHING', xPosition: 600.0, side: 'A', toolSize: 0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DB1', headType: 'PUNCHING', xPosition: 200.0, side: 'B', toolSize: 0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DB2', headType: 'PUNCHING', xPosition: 400.0, side: 'B', toolSize: 0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DB3', headType: 'PUNCHING', xPosition: 600.0, side: 'B', toolSize: 0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'Marking', headType: 'MARKING', xPosition: 50.0, side: 'NA', markingCassettes: 4, toolSize: 0, isActive: true },
          { headName: 'Cutter', headType: 'CUTTING', xPosition: 0.0, side: 'NA', toolSize: 0, isActive: true },
        ],
      },
    },
  });

  // 3. Create Real Innovance H3U / H5U PLC Register Tag Mapping
  await prisma.plcConfig.create({
    data: {
      id: 'default-plc',
      name: 'Innovance H3U/H5U PLC',
      ipAddress: '192.168.1.10',
      port: 502,
      endpointUrl: 'modbus://192.168.1.10:502',
      protocol: 'MODBUS_TCP',
      isSimulator: false,
      tags: {
        create: [
          // AXIS & DRO REGISTERS
          { tagName: 'Feed_Axis_Current_Position', tagAddress: 'D1000', dataType: 'Float', category: 'AXIS_DRO', accessMode: 'READ', unit: 'mm' },
          { tagName: 'Feed_Axis_Target_Position', tagAddress: 'D1002', dataType: 'Float', category: 'AXIS_DRO', accessMode: 'READ_WRITE', unit: 'mm' },
          { tagName: 'Feed_Axis_Current_Speed', tagAddress: 'D1004', dataType: 'Float', category: 'AXIS_DRO', accessMode: 'READ', unit: 'm/min' },
          { tagName: 'Feed_Axis_Jog_Forward', tagAddress: 'M104', dataType: 'Boolean', category: 'AXIS_DRO', accessMode: 'READ_WRITE' },
          { tagName: 'Feed_Axis_Jog_Reverse', tagAddress: 'M105', dataType: 'Boolean', category: 'AXIS_DRO', accessMode: 'READ_WRITE' },

          // HEADS & TOOLING COILS
          { tagName: 'Head_DA1_Punch_Trigger', tagAddress: 'M110', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DA2_Punch_Trigger', tagAddress: 'M111', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DA3_Punch_Trigger', tagAddress: 'M112', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DB1_Punch_Trigger', tagAddress: 'M113', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DB2_Punch_Trigger', tagAddress: 'M114', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DB3_Punch_Trigger', tagAddress: 'M115', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Marking_Trigger', tagAddress: 'M120', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Shear_Cut_Trigger', tagAddress: 'M121', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },

          // HYDRAULICS & CLAMPS
          { tagName: 'Hydraulic_Pump_Running', tagAddress: 'M130', dataType: 'Boolean', category: 'HYDRAULIC', accessMode: 'READ_WRITE' },
          { tagName: 'Hydraulic_Pressure_Bar', tagAddress: 'D1010', dataType: 'Float', category: 'HYDRAULIC', accessMode: 'READ', unit: 'bar' },
          { tagName: 'Infeed_Clamp_Engaged', tagAddress: 'M131', dataType: 'Boolean', category: 'CLAMP', accessMode: 'READ_WRITE' },
          { tagName: 'Carriage_Clamp_Engaged', tagAddress: 'M132', dataType: 'Boolean', category: 'CLAMP', accessMode: 'READ_WRITE' },
          { tagName: 'Outfeed_Clamp_Engaged', tagAddress: 'M133', dataType: 'Boolean', category: 'CLAMP', accessMode: 'READ_WRITE' },

          // INTERLOCKS & AUTO CYCLE
          { tagName: 'Emergency_Stop_OK', tagAddress: 'X3', dataType: 'Boolean', category: 'INTERLOCK', accessMode: 'READ' },
          { tagName: 'Safety_Guards_Closed', tagAddress: 'X4', dataType: 'Boolean', category: 'INTERLOCK', accessMode: 'READ' },
          { tagName: 'Machine_Auto_Mode', tagAddress: 'M100', dataType: 'Boolean', category: 'SYSTEM', accessMode: 'READ_WRITE' },
          { tagName: 'Auto_Cycle_Start', tagAddress: 'M101', dataType: 'Boolean', category: 'AUTO_CYCLE', accessMode: 'READ_WRITE' },
          { tagName: 'Auto_Cycle_Pause', tagAddress: 'M102', dataType: 'Boolean', category: 'AUTO_CYCLE', accessMode: 'READ_WRITE' },
          { tagName: 'Auto_Cycle_Abort', tagAddress: 'M103', dataType: 'Boolean', category: 'AUTO_CYCLE', accessMode: 'READ_WRITE' },
        ],
      },
    },
  });

  // 4. Create Standard Industrial CNC Angle Processing Recipes
  await prisma.itemRecipe.create({
    data: {
      itemCode: 'ISA-100x100x10-BRACE-L1',
      itemName: 'Transmission Tower Diagonal Cross-Brace L1',
      description: 'High-tensile 3200mm angle bar with 6-head staggered punch pattern, automated part stamping, and cut-off.',
      angleWidthA: 100.0,
      angleWidthB: 100.0,
      thickness: 10.0,
      totalLength: 3200.0,
      measurementType: 'ABSOLUTE',
      steps: {
        create: [
          { stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 120.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 280.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 3, operationType: 'PUNCH', side: 'B', xPosition: 120.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 280.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 5, operationType: 'MARK', side: 'NA', xPosition: 550.0, yPosition: 0.0, markingText: 'TWR-A101' },
          { stepNumber: 6, operationType: 'PUNCH', side: 'A', xPosition: 1600.0, yPosition: 50.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 7, operationType: 'PUNCH', side: 'B', xPosition: 1600.0, yPosition: 50.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 8, operationType: 'PUNCH', side: 'A', xPosition: 2920.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 9, operationType: 'PUNCH', side: 'A', xPosition: 3080.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 10, operationType: 'PUNCH', side: 'B', xPosition: 2920.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 11, operationType: 'PUNCH', side: 'B', xPosition: 3080.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 12, operationType: 'CUT', side: 'NA', xPosition: 3200.0, yPosition: 0.0, isCutOff: true },
        ],
      },
    },
  });

  await prisma.itemRecipe.create({
    data: {
      itemCode: 'ISA-75x75x6-SOLAR-S2',
      itemName: 'Solar Tracker Structure Leg Strut S2',
      description: 'Galvanized 2400mm angle leg strut with slotted & round mounting holes and part stamping.',
      angleWidthA: 75.0,
      angleWidthB: 75.0,
      thickness: 6.0,
      totalLength: 2400.0,
      measurementType: 'ABSOLUTE',
      steps: {
        create: [
          { stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 100.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
          { stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 250.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
          { stepNumber: 3, operationType: 'PUNCH', side: 'B', xPosition: 100.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
          { stepNumber: 4, operationType: 'MARK', side: 'NA', xPosition: 400.0, yPosition: 0.0, markingText: 'SOL-P99' },
          { stepNumber: 5, operationType: 'PUNCH', side: 'A', xPosition: 1200.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
          { stepNumber: 6, operationType: 'PUNCH', side: 'B', xPosition: 1200.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
          { stepNumber: 7, operationType: 'PUNCH', side: 'A', xPosition: 2280.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
          { stepNumber: 8, operationType: 'CUT', side: 'NA', xPosition: 2400.0, yPosition: 0.0, isCutOff: true },
        ],
      },
    },
  });

  await prisma.itemRecipe.create({
    data: {
      itemCode: 'ISA-130x130x12-SUB-M1',
      itemName: 'High-Voltage Substation Gantry Column M1',
      description: 'Heavy 4500mm structural angle with dual-gauge Ø22 and Ø26 punch patterns.',
      angleWidthA: 130.0,
      angleWidthB: 130.0,
      thickness: 12.0,
      totalLength: 4500.0,
      measurementType: 'ABSOLUTE',
      steps: {
        create: [
          { stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 150.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 150.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 3, operationType: 'PUNCH', side: 'B', xPosition: 150.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 150.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 5, operationType: 'MARK', side: 'NA', xPosition: 700.0, yPosition: 0.0, markingText: 'SUB-M01' },
          { stepNumber: 6, operationType: 'PUNCH', side: 'A', xPosition: 2250.0, yPosition: 65.0, toolSize: 26.0, toolShape: 'ROUND' },
          { stepNumber: 7, operationType: 'PUNCH', side: 'B', xPosition: 2250.0, yPosition: 65.0, toolSize: 26.0, toolShape: 'ROUND' },
          { stepNumber: 8, operationType: 'PUNCH', side: 'A', xPosition: 4350.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 9, operationType: 'PUNCH', side: 'A', xPosition: 4350.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 10, operationType: 'PUNCH', side: 'B', xPosition: 4350.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 11, operationType: 'PUNCH', side: 'B', xPosition: 4350.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
          { stepNumber: 12, operationType: 'CUT', side: 'NA', xPosition: 4500.0, yPosition: 0.0, isCutOff: true },
        ],
      },
    },
  });

  await prisma.itemRecipe.create({
    data: {
      itemCode: 'ISA-90x90x8-GANTRY-G3',
      itemName: 'Overhead Crane Gantry Tie Member G3',
      description: 'Medium 1800mm tie angle bar with high-speed 6-hole punching sequence.',
      angleWidthA: 90.0,
      angleWidthB: 90.0,
      thickness: 8.0,
      totalLength: 1800.0,
      measurementType: 'ABSOLUTE',
      steps: {
        create: [
          { stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 100.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 2, operationType: 'PUNCH', side: 'B', xPosition: 100.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 3, operationType: 'MARK', side: 'NA', xPosition: 350.0, yPosition: 0.0, markingText: 'GNT-303' },
          { stepNumber: 4, operationType: 'PUNCH', side: 'A', xPosition: 900.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 5, operationType: 'PUNCH', side: 'B', xPosition: 900.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 6, operationType: 'PUNCH', side: 'A', xPosition: 1700.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 7, operationType: 'PUNCH', side: 'B', xPosition: 1700.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
          { stepNumber: 8, operationType: 'CUT', side: 'NA', xPosition: 1800.0, yPosition: 0.0, isCutOff: true },
        ],
      },
    },
  });

  console.log('✅ Clean production database seeded with real Innovance PLC registers and 4 production recipes.');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
