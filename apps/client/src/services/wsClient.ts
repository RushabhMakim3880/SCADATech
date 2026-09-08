import { WsClientMessage, WsServerMessage } from '@innovance-hmi/shared';

export interface WsCallbacks {
  onOpen?: () => void;
  onMessage?: (msg: WsServerMessage | any) => void;
  onClose?: () => void;
  onError?: (err: any) => void;
}

class WsClient {
  private socket: WebSocket | null = null;
  private url: string = '';
  private callbacks: WsCallbacks = {};
  private reconnectTimer: any = null;
  private disconnectTimer: any = null;
  private isExplicitlyClosed: boolean = false;

  public connect(url: string, callbacks: WsCallbacks) {
    this.url = url;
    this.callbacks = callbacks;
    this.isExplicitlyClosed = false;

    // Clear any pending disconnect from React StrictMode dev unmount
    if (this.disconnectTimer) {
      clearTimeout(this.disconnectTimer);
      this.disconnectTimer = null;
    }

    // If socket is already open, notify immediately and retain socket
    if (this.socket && this.socket.readyState === WebSocket.OPEN) {
      this.callbacks.onOpen?.();
      return;
    }

    // If socket is already connecting, let it complete
    if (this.socket && this.socket.readyState === WebSocket.CONNECTING) {
      return;
    }

    this.createSocket();
  }

  private createSocket() {
    try {
      this.socket = new WebSocket(this.url);

      this.socket.onopen = () => {
        if (this.isExplicitlyClosed) {
          this.socket?.close();
          return;
        }
        console.log('✅ Connected to HMI WebSocket gateway');
        this.callbacks.onOpen?.();
      };

      this.socket.onmessage = (event) => {
        try {
          const msg = JSON.parse(event.data);
          this.callbacks.onMessage?.(msg);
        } catch (e) {
          console.error('WS Parse Error', e);
        }
      };

      this.socket.onerror = (err) => {
        this.callbacks.onError?.(err);
      };

      this.socket.onclose = () => {
        this.callbacks.onClose?.();
        // Auto-reconnect if not explicitly closed
        if (!this.isExplicitlyClosed) {
          clearTimeout(this.reconnectTimer);
          this.reconnectTimer = setTimeout(() => {
            this.createSocket();
          }, 2000);
        }
      };
    } catch (err) {
      console.warn('WS initialization failed', err);
    }
  }

  public disconnect() {
    // Debounce disconnect so React StrictMode dev unmount doesn't prematurely kill a connecting socket
    if (this.disconnectTimer) {
      clearTimeout(this.disconnectTimer);
    }
    this.disconnectTimer = setTimeout(() => {
      this.performDisconnect();
    }, 250);
  }

  private performDisconnect() {
    this.isExplicitlyClosed = true;
    clearTimeout(this.reconnectTimer);

    if (this.socket) {
      const sock = this.socket;
      this.socket = null;
      if (sock.readyState === WebSocket.OPEN) {
        sock.close();
      } else if (sock.readyState === WebSocket.CONNECTING) {
        // Prevent browser "WebSocket is closed before the connection is established" error
        sock.onopen = () => {
          sock.close();
        };
        sock.onerror = () => {};
      }
    }
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
