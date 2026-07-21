const WebSocket = require("ws");
const config = require("./config");
const { sendToBackend } = require("./utils");
const cookie = require('cookie');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');


class WSServer {
    constructor() {
        this.wss = new WebSocket.Server({ port: config.ws.port });
        this.clientSocket = null;
        this.client = null;
        this.setup();
        console.log(`✅ WebSocket server started on port ${config.ws.port}`);
        this.wss.on("error", async (err) => {
            console.error("❌ WebSocket Server Error:", err.message);
            await sendToBackend({ type: "wsError", message: `WebSocket Server Error: ${err.message}` });
        });
        this.wss.on("listening", () => {
            console.log(`✅ WebSocket Server is listening on port ${config.ws.port}`);
        });
        this.wss.on("close", () => {
            console.log("WebSocket Server closed");
        });
    }

    setup() {
        this.wss.on("connection", (ws, req) => {
            console.log("New WebSocket connection attempt");
            if (this.clientSocket) {
                ws.send(JSON.stringify({ error: "Only one client allowed." }));
                ws.close();
                return;
            }

            //auth validation via jwt token.
            const cookies = cookie.parse(req.headers.cookie || '');
            const token = cookies.jwt;
            if (!token) {
                ws.send(JSON.stringify({ type: 'error', message: 'Authentication token is missing' }));
                return ws.close();
            }

            const decoded = this.verifyToken(token);
            if (!decoded || !decoded.data.userId) {
                ws.send(JSON.stringify({ type: 'error', message: 'Invalid token' }));
                return ws.close();
            }


            this.userId = decoded.data.userId;

            this.clientSocket = ws;
            console.log("✅ WebSocket client connected");

            // send welcome message
            ws.send(JSON.stringify({ type: 'welcome', message: 'Welcome to the WebSocket server!' }));

            ws.on("message", async (msg) => {
                try {
                    const data = JSON.parse(msg);

                } catch (err) {
                    await sendToBackend({ type: "wsError", message: `WS Message Error: ${err.message}` });
                    ws.send(JSON.stringify({ error: err.message }));
                }
            });

            ws.on("close", () => {
                console.log("WebSocket client disconnected");
                this.clientSocket = null;
                this.userId = null;
            });
        });
    }

    broadcast(data) {
        if (this.clientSocket && this.clientSocket.readyState === WebSocket.OPEN) {
            this.clientSocket.send(JSON.stringify(data));
        }
    }

    verifyToken(token) {
        if (!config.jwt.JWT_SECRET || !config.jwt.JWT_ALGORITHM) {
            console.error('JWT_SECRET or JWT_ALGORITHM is not set in environment variables');
            return null;
        }
        try {
            return jwt.verify(token, config.jwt.JWT_SECRET, { algorithms: [config.jwt.JWT_ALGORITHM] });
        } catch (err) {
            console.error('JWT verification failed:', err.message);
            return null;
        }
    }
}

module.exports = new WSServer();
