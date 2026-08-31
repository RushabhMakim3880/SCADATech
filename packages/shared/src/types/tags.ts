export type TagDataType =
  | 'Boolean'
  | 'Int16'
  | 'UInt16'
  | 'Int32'
  | 'UInt32'
  | 'Float'
  | 'Double'
  | 'String';

export type TagAccessMode = 'READ' | 'WRITE' | 'READ_WRITE';

export type TagCategory =
  | 'AXIS_DRO'
  | 'HEAD_CONTROL'
  | 'HYDRAULIC'
  | 'CLAMP'
  | 'INTERLOCK'
  | 'ALARM'
  | 'AUTO_CYCLE'
  | 'SYSTEM';

export interface PlcTagDefinition {
  id: string;
  tagAddress: string; // e.g. "ns=2;s=Application.GVL.rFeedAxisPos" or Modbus address
  tagName: string; // e.g. "Feed_Axis_Current_Position_DRO"
  tagDescription?: string;
  dataType: TagDataType;
  category: TagCategory;
  accessMode: TagAccessMode;
  unit?: string; // mm, bar, rpm, °C, bool
  scalingFactor?: number;
  offset?: number;
  pollIntervalMs?: number; // 0 for continuous subscription
  currentValue?: boolean | number | string;
  lastUpdated?: string;
}

export interface TagValueUpdate {
  tagId: string;
  tagName: string;
  value: boolean | number | string;
  timestamp: number;
  quality: 'GOOD' | 'BAD' | 'UNCERTAIN';
}

export interface TagBatchWriteRequest {
  tags: Array<{
    tagAddress: string;
    value: boolean | number | string;
    dataType: TagDataType;
  }>;
}
