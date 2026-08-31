import {
  OPCUAClient,
  ClientSession,
  ClientSubscription,
  AttributeIds,
  DataType,
  StatusCodes,
  TimestampsToReturn,
  DataValue,
  Variant,
  MonitoringParametersOptions,
  ReadValueIdOptions,
  WriteValueOptions,
} from 'node-opcua';
import { TagDataType, TagValueUpdate } from '@innovance-hmi/shared';

export class OpcUaService {
  private client: OPCUAClient | null = null;
  private session: ClientSession | null = null;
  private subscription: ClientSubscription | null = null;
  private isConnected = false;
  private endpointUrl: string = 'opc.tcp://192.168.1.10:4840';
  private tagChangeCallback: ((update: TagValueUpdate) => void) | null = null;
  private statusChangeCallback: ((status: { connected: boolean; message: string }) => void) | null = null;

  constructor() {}

  public onTagChange(callback: (update: TagValueUpdate) => void) {
    this.tagChangeCallback = callback;
  }

  public onStatusChange(callback: (status: { connected: boolean; message: string }) => void) {
    this.statusChangeCallback = callback;
  }

  public async connect(endpointUrl: string): Promise<boolean> {
    this.endpointUrl = endpointUrl;
    this.client = OPCUAClient.create({
      endpointMustExist: false,
      keepSessionAlive: true,
      connectionStrategy: {
        initialDelay: 1000,
        maxRetry: 5,
      },
    });

    try {
      await this.client.connect(endpointUrl);
      this.session = await this.client.createSession();
      this.isConnected = true;
      this.statusChangeCallback?.({ connected: true, message: `Connected to OPC-UA at ${endpointUrl}` });
      return true;
    } catch (err: any) {
      this.isConnected = false;
      this.statusChangeCallback?.({ connected: false, message: `Connect error: ${err.message}` });
      return false;
    }
  }

  public async disconnect(): Promise<void> {
    try {
      if (this.subscription) {
        await this.subscription.terminate();
        this.subscription = null;
      }
      if (this.session) {
        await this.session.close();
        this.session = null;
      }
      if (this.client) {
        await this.client.disconnect();
        this.client = null;
      }
      this.isConnected = false;
      this.statusChangeCallback?.({ connected: false, message: 'Disconnected from PLC' });
    } catch (err: any) {
      console.error('OPC-UA Disconnect error:', err.message);
    }
  }

  public async subscribeToTags(
    tags: Array<{ tagId: string; tagAddress: string; tagName: string; dataType: TagDataType }>
  ): Promise<void> {
    if (!this.session || !this.isConnected) return;

    if (this.subscription) {
      await this.subscription.terminate();
      this.subscription = null;
    }

    this.subscription = ClientSubscription.create(this.session, {
      requestedPublishingInterval: 50, // 50ms fast polling for DRO
      requestedLifetimeCount: 100,
      requestedMaxKeepAliveCount: 10,
      maxNotificationsPerPublish: 100,
      publishingEnabled: true,
      priority: 10,
    });

    for (const tag of tags) {
      const itemToMonitor: ReadValueIdOptions = {
        nodeId: tag.tagAddress,
        attributeId: AttributeIds.Value,
      };

      const parameters: MonitoringParametersOptions = {
        samplingInterval: 50,
        discardOldest: true,
        queueSize: 10,
      };

      const monitoredItem = await this.subscription.monitor(
        itemToMonitor,
        parameters,
        TimestampsToReturn.Both
      );

      monitoredItem.on('changed', (dataValue: DataValue) => {
        const val = dataValue.value?.value;
        if (val !== undefined && this.tagChangeCallback) {
          this.tagChangeCallback({
            tagId: tag.tagId,
            tagName: tag.tagName,
            value: val,
            timestamp: Date.now(),
            quality: dataValue.statusCode === StatusCodes.Good ? 'GOOD' : 'BAD',
          });
        }
      });
    }
  }

  public async writeTag(tagAddress: string, value: any, dataType: TagDataType): Promise<boolean> {
    if (!this.session || !this.isConnected) return false;

    const opcuaType = this.mapDataTypeToOpcUa(dataType);
    const nodeToWrite: WriteValueOptions = {
      nodeId: tagAddress,
      attributeId: AttributeIds.Value,
      value: {
        value: {
          dataType: opcuaType,
          value: value,
        },
      },
    };

    try {
      const statusCode = await this.session.write(nodeToWrite);
      return statusCode === StatusCodes.Good;
    } catch (err) {
      console.error(`Error writing tag ${tagAddress}:`, err);
      return false;
    }
  }

  private mapDataTypeToOpcUa(type: TagDataType): DataType {
    switch (type) {
      case 'Boolean':
        return DataType.Boolean;
      case 'Int16':
        return DataType.Int16;
      case 'UInt16':
        return DataType.UInt16;
      case 'Int32':
        return DataType.Int32;
      case 'UInt32':
        return DataType.UInt32;
      case 'Float':
        return DataType.Float;
      case 'Double':
        return DataType.Double;
      case 'String':
      default:
        return DataType.String;
    }
  }

  public getConnected(): boolean {
    return this.isConnected;
  }
}
