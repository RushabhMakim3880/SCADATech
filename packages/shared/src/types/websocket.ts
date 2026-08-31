import { TagValueUpdate } from './tags.js';
import { LiveProductionStatus } from './production.js';
import { ActiveAlarm } from './alarm.js';

export type WsClientMessageType =
  | 'SUBSCRIBE_TAGS'
  | 'UNSUBSCRIBE_TAGS'
  | 'WRITE_TAG'
  | 'JOG_AXIS_START'
  | 'JOG_AXIS_STOP'
  | 'TOGGLE_VALVE'
  | 'START_PRODUCTION'
  | 'PAUSE_PRODUCTION'
  | 'STOP_PRODUCTION'
  | 'ACKNOWLEDGE_ALARM'
  | 'PING';

export type WsServerMessageType =
  | 'PLC_STATUS'
  | 'TAG_UPDATES'
  | 'PRODUCTION_STATUS'
  | 'ACTIVE_ALARMS'
  | 'OPERATION_LOG'
  | 'COMMAND_RESULT'
  | 'PONG';

export interface WsClientMessage {
  type: WsClientMessageType;
  payload?: any;
  requestId?: string;
}

export interface WsServerMessage {
  type: WsServerMessageType;
  timestamp: number;
  payload: any;
  requestId?: string;
}
