import { TagDataType, TagValueUpdate } from '@innovance-hmi/shared';

export interface OpcUaNodeMapping {
  nodeId: string;
  tagName: string;
  dataType: TagDataType;
}

export interface PlcConnectionStatus {
  connected: boolean;
  endpointUrl?: string;
  isSimulator: boolean;
  protocol: string;
  lastHeartbeat: number;
  activeSubscriptionsCount: number;
}
