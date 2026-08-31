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

  // 1. Create Default Operator & Admin Users
  await prisma.user.upsert({
    where: { username: 'admin' },
    update: {},
    create: {
      username: 'admin',
      name: 'Plant Administrator',
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

  console.log('✅ Clean production database seeded with real Innovance PLC registers. Zero dummy recipes.');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
