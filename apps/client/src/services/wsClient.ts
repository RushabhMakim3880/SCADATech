import { WsClientMessage } from '@innovance-hmi/shared';

class WsClient {
  private socket: WebSocket | null = null;

  public setSocket(ws: WebSocket | null) {
    this.socket = ws;
  }

  public send(msg: WsClientMessage) {
    if (this.socket && this.socket.readyState === WebSocket.OPEN) {
      this.socket.send(JSON.stringify(msg));
    }
  }

  public jogStart(direction: 'FWD' | 'REV', speed: number = 40.0) {
    this.send({
      type: 'JOG_AXIS_START',
      payload: { direction, speed },
    });
  }

  public jogStop(direction: 'FWD' | 'REV') {
    this.send({
      type: 'JOG_AXIS_STOP',
      payload: { direction },
    });
  }

  public toggleValve(valve: 'infeed' | 'carriage' | 'outfeed' | 'pump') {
    this.send({
      type: 'TOGGLE_VALVE',
      payload: { valve },
    });
  }

  public writeTag(tagName: string, value: any, dataType: string = 'Boolean') {
    this.send({
      type: 'WRITE_TAG',
      payload: { tagName, value, dataType },
    });
  }
}

export const wsClient = new WsClient();
