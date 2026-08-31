import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Seeding Innovance 6-Head Angle Punching Machine Database...');

  // 1. Create Default Operator & Admin Users
  await prisma.user.upsert({
    where: { username: 'admin' },
    update: {},
    create: {
      username: 'admin',
      name: 'System Administrator',
      role: 'ADMIN',
      pinCode: '9999',
    },
  });

  await prisma.user.upsert({
    where: { username: 'operator' },
    update: {},
    create: {
      username: 'operator',
      name: 'Line Operator 1',
      role: 'OPERATOR',
      pinCode: '1234',
    },
  });

  // 2. Create Machine Configuration (6 Heads + Marking + Cutter)
  const machine = await prisma.machine.upsert({
    where: { machineCode: 'HPT-INNOVANCE-6H-01' },
    update: {},
    create: {
      machineCode: 'HPT-INNOVANCE-6H-01',
      machineName: 'Innovance 6-Head CNC Angle Line',
      machineType: 'SKIPPER',
      headCount: 6,
      minAngleSize: 40.0,
      maxAngleSize: 200.0,
      minThickness: 3.0,
      maxThickness: 20.0,
      maxBarLength: 12000.0,
      details: {
        create: [
          // Side A Heads (Punching)
          { headName: 'DA1', headType: 'PUNCHING', xPosition: 200.0, side: 'A', toolSize: 14.0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DA2', headType: 'PUNCHING', xPosition: 400.0, side: 'A', toolSize: 18.0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DA3', headType: 'PUNCHING', xPosition: 600.0, side: 'A', toolSize: 22.0, toolShape: 'ROUND', maxToolSize: 32.0 },
          // Side B Heads (Punching)
          { headName: 'DB1', headType: 'PUNCHING', xPosition: 200.0, side: 'B', toolSize: 14.0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DB2', headType: 'PUNCHING', xPosition: 400.0, side: 'B', toolSize: 18.0, toolShape: 'ROUND', maxToolSize: 32.0 },
          { headName: 'DB3', headType: 'PUNCHING', xPosition: 600.0, side: 'B', toolSize: 22.0, toolShape: 'ROUND', maxToolSize: 32.0 },
          // Marking Unit
          { headName: 'Marking', headType: 'MARKING', xPosition: 50.0, side: 'NA', markingCassettes: 4, toolSize: 0, isActive: true },
          // Shearing Cutter
          { headName: 'Cutter', headType: 'CUTTING', xPosition: 0.0, side: 'NA', toolSize: 0, isActive: true },
        ],
      },
    },
  });

  // 3. Create PLC Configuration & Standard Tags
  const plc = await prisma.plcConfig.upsert({
    where: { id: 'default-plc' },
    update: {},
    create: {
      id: 'default-plc',
      name: 'Innovance AM600 / H5U PLC',
      ipAddress: '192.168.1.10',
      port: 4840,
      endpointUrl: 'opc.tcp://192.168.1.10:4840',
      protocol: 'OPC-UA',
      isSimulator: true,
      tags: {
        create: [
          // AXIS & DRO
          { tagName: 'Feed_Axis_Current_Position', tagAddress: 'ns=2;s=Application.GVL.rFeedPos', dataType: 'Float', category: 'AXIS_DRO', accessMode: 'READ', unit: 'mm' },
          { tagName: 'Feed_Axis_Target_Position', tagAddress: 'ns=2;s=Application.GVL.rTargetPos', dataType: 'Float', category: 'AXIS_DRO', accessMode: 'READ_WRITE', unit: 'mm' },
          { tagName: 'Feed_Axis_Current_Speed', tagAddress: 'ns=2;s=Application.GVL.rFeedSpeed', dataType: 'Float', category: 'AXIS_DRO', accessMode: 'READ', unit: 'm/min' },
          { tagName: 'Feed_Axis_Jog_Forward', tagAddress: 'ns=2;s=Application.GVL.bJogFwd', dataType: 'Boolean', category: 'AXIS_DRO', accessMode: 'READ_WRITE' },
          { tagName: 'Feed_Axis_Jog_Reverse', tagAddress: 'ns=2;s=Application.GVL.bJogRev', dataType: 'Boolean', category: 'AXIS_DRO', accessMode: 'READ_WRITE' },
          
          // HEADS & CYLINDERS
          { tagName: 'Head_DA1_Punch_Trigger', tagAddress: 'ns=2;s=Application.GVL.bPunchDA1', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DA2_Punch_Trigger', tagAddress: 'ns=2;s=Application.GVL.bPunchDA2', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DA3_Punch_Trigger', tagAddress: 'ns=2;s=Application.GVL.bPunchDA3', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DB1_Punch_Trigger', tagAddress: 'ns=2;s=Application.GVL.bPunchDB1', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DB2_Punch_Trigger', tagAddress: 'ns=2;s=Application.GVL.bPunchDB2', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Head_DB3_Punch_Trigger', tagAddress: 'ns=2;s=Application.GVL.bPunchDB3', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Marking_Trigger', tagAddress: 'ns=2;s=Application.GVL.bMarkingTrigger', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          { tagName: 'Shear_Cut_Trigger', tagAddress: 'ns=2;s=Application.GVL.bCutterTrigger', dataType: 'Boolean', category: 'HEAD_CONTROL', accessMode: 'READ_WRITE' },
          
          // HYDRAULICS & CLAMPS
          { tagName: 'Hydraulic_Pump_Running', tagAddress: 'ns=2;s=Application.GVL.bHydraulicPump', dataType: 'Boolean', category: 'HYDRAULIC', accessMode: 'READ_WRITE' },
          { tagName: 'Hydraulic_Pressure_Bar', tagAddress: 'ns=2;s=Application.GVL.rPressureBar', dataType: 'Float', category: 'HYDRAULIC', accessMode: 'READ', unit: 'bar' },
          { tagName: 'Infeed_Clamp_Engaged', tagAddress: 'ns=2;s=Application.GVL.bInfeedClamp', dataType: 'Boolean', category: 'CLAMP', accessMode: 'READ_WRITE' },
          { tagName: 'Carriage_Clamp_Engaged', tagAddress: 'ns=2;s=Application.GVL.bCarriageClamp', dataType: 'Boolean', category: 'CLAMP', accessMode: 'READ_WRITE' },
          { tagName: 'Outfeed_Clamp_Engaged', tagAddress: 'ns=2;s=Application.GVL.bOutfeedClamp', dataType: 'Boolean', category: 'CLAMP', accessMode: 'READ_WRITE' },

          // INTERLOCKS & SAFETY
          { tagName: 'Emergency_Stop_OK', tagAddress: 'ns=2;s=Application.GVL.bEStopOk', dataType: 'Boolean', category: 'INTERLOCK', accessMode: 'READ' },
          { tagName: 'Safety_Guards_Closed', tagAddress: 'ns=2;s=Application.GVL.bGuardsOk', dataType: 'Boolean', category: 'INTERLOCK', accessMode: 'READ' },
          { tagName: 'Machine_Auto_Mode', tagAddress: 'ns=2;s=Application.GVL.bAutoMode', dataType: 'Boolean', category: 'SYSTEM', accessMode: 'READ_WRITE' },
          { tagName: 'Auto_Cycle_Start', tagAddress: 'ns=2;s=Application.GVL.bCycleStart', dataType: 'Boolean', category: 'AUTO_CYCLE', accessMode: 'READ_WRITE' },
          { tagName: 'Auto_Cycle_Pause', tagAddress: 'ns=2;s=Application.GVL.bCyclePause', dataType: 'Boolean', category: 'AUTO_CYCLE', accessMode: 'READ_WRITE' },
          { tagName: 'Auto_Cycle_Abort', tagAddress: 'ns=2;s=Application.GVL.bCycleAbort', dataType: 'Boolean', category: 'AUTO_CYCLE', accessMode: 'READ_WRITE' },
        ],
      },
    },
  });

  // 4. Sample Item Recipe: 75x75x6 mm Tower Leg Angle (1500 mm)
  await prisma.itemRecipe.upsert({
    where: { itemCode: 'ANG-75-L1500' },
    update: {},
    create: {
      itemCode: 'ANG-75-L1500',
      itemName: 'Transmission Tower Angle 75x75x6',
      description: 'Standard 4-hole punched diagonal angle member',
      angleWidthA: 75.0,
      angleWidthB: 75.0,
      thickness: 6.0,
      totalLength: 1500.0,
      measurementType: 'ABSOLUTE',
      steps: {
        create: [
          { stepNumber: 1, operationType: 'MARK', side: 'NA', xPosition: 50.0, yPosition: 0.0, markingText: 'T1-L15', isCutOff: false },
          { stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 120.0, yPosition: 35.0, toolSize: 18.0, toolShape: 'ROUND', isCutOff: false },
          { stepNumber: 3, operationType: 'PUNCH', side: 'A', xPosition: 300.0, yPosition: 35.0, toolSize: 18.0, toolShape: 'ROUND', isCutOff: false },
          { stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 120.0, yPosition: 35.0, toolSize: 18.0, toolShape: 'ROUND', isCutOff: false },
          { stepNumber: 5, operationType: 'PUNCH', side: 'B', xPosition: 300.0, yPosition: 35.0, toolSize: 18.0, toolShape: 'ROUND', isCutOff: false },
          { stepNumber: 6, operationType: 'PUNCH', side: 'A', xPosition: 1380.0, yPosition: 35.0, toolSize: 18.0, toolShape: 'ROUND', isCutOff: false },
          { stepNumber: 7, operationType: 'PUNCH', side: 'B', xPosition: 1380.0, yPosition: 35.0, toolSize: 18.0, toolShape: 'ROUND', isCutOff: false },
          { stepNumber: 8, operationType: 'CUT', side: 'NA', xPosition: 1500.0, yPosition: 0.0, isCutOff: true, remarks: 'Cut-off blade' },
        ],
      },
    },
  });

  // 5. Standard Alarms
  await prisma.alarmDefinition.upsert({
    where: { alarmCode: 'ALM_ESTOP' },
    update: {},
    create: {
      alarmCode: 'ALM_ESTOP',
      alarmName: 'Emergency Stop Activated',
      description: 'Emergency stop circuit is open. Check all physical E-Stop buttons.',
      severity: 'EMERGENCY',
      triggerTagAddress: 'ns=2;s=Application.GVL.bEStopOk',
      expectedValue: 'true',
      correctiveAction: 'Twist to release pressed E-stop buttons, then reset safety relay.',
    },
  });

  await prisma.alarmDefinition.upsert({
    where: { alarmCode: 'ALM_HYD_PRESS_LOW' },
    update: {},
    create: {
      alarmCode: 'ALM_HYD_PRESS_LOW',
      alarmName: 'Low Hydraulic Pressure',
      description: 'Hydraulic system pressure dropped below minimum operating limit (120 bar).',
      severity: 'CRITICAL',
      triggerTagAddress: 'ns=2;s=Application.GVL.rPressureBar',
      expectedValue: '120.0',
      correctiveAction: 'Check hydraulic oil level, filter clogging, and pump motor circuit breaker.',
    },
  });

  await prisma.alarmDefinition.upsert({
    where: { alarmCode: 'ALM_GUARD_OPEN' },
    update: {},
    create: {
      alarmCode: 'ALM_GUARD_OPEN',
      alarmName: 'Safety Interlock Guard Open',
      description: 'Machine safety door / interlock guard is not fully closed.',
      severity: 'WARNING',
      triggerTagAddress: 'ns=2;s=Application.GVL.bGuardsOk',
      expectedValue: 'true',
      correctiveAction: 'Close all safety guard doors firmly before engaging auto cycle.',
    },
  });

  console.log('✅ Database seeded successfully!');
}

main()
  .catch((e) => {
    console.error('❌ Seeding error:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
