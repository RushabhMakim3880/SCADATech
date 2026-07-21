const express = require("express");
const bodyParser = require("body-parser");
const { sendToBackend } = require("./utils");
const config = require("./config");
const plcManager = require("./plcManager");

const wsServer = require("./wsServer");

const app = express();
app.use(bodyParser.json());

function authenticateToken(req, res, next) {
    const authHeader = req.headers['x-internal-token'];
    // console.log("Received auth header:", JSON.stringify(req.headers));
    if (!authHeader) {
        return res.status(401).json({ error: "Authorization header missing" });
    }

    if (authHeader !== config.api.internalApiToken) {
        return res.status(403).json({ error: "Invalid token" });
    }

    next();
}


// Write tags endpoint
app.post("/api/writeTags", authenticateToken, async (req, res) => {
    try {
        await plcManager.writeTags(req.body.tags);
        res.json({ success: true });
        // wsServer.broadcast({ type: "commandResult", command: "write", success: true });
    } catch (err) {
        await sendToBackend({ type: "apiError", message: `API Write Error: ${err.message}` });
        res.status(500).json({ error: err.message });
        wsServer.broadcast({ type: "commandResult", command: "write", success: false, error: err.message });
    }
});

app.post("/api/readTagsContinue", authenticateToken, async (req, res) => {
    try {
        const values = await plcManager.setReadTagsToPoll(req.body.tags);
        res.json({ success: true, data: values });
        wsServer.broadcast({ type: "commandResult", command: "read", data: values });
    } catch (err) {
        await sendToBackend({ type: "apiError", message: `API Read Error: ${err.message}` });
        res.status(500).json({ error: err.message });
    }
});

// Read tags endpoint
app.post("/api/readTags", authenticateToken, async (req, res) => {
    try {
        const values = await plcManager.readTags(Object.keys(req.body.tags));
        res.json({ success: true, data: values });
        wsServer.broadcast({ type: "commandResult", command: "read", data: values });
    } catch (err) {
        await sendToBackend({ type: "apiError", message: `API Read Error: ${err.message}` });
        res.status(500).json({ error: err.message });
    }
});

app.post("/api/initPlc", authenticateToken, async (req, res) => {
    try {
        const plcConfig = {
            host: req.body.plcHost,
            port: req.body.plcPort,
            protocol: req.body.plcProtocol
        };

        await plcManager.disconnect(); // Disconnect any existing PLC first
        await plcManager.init(plcConfig, wsServer);

        res.json({ success: true, message: "PLC configuration initialized" });
    } catch (err) {
        await sendToBackend({ type: "apiError", message: `PLC Init API Error: ${err.message}` });
        res.status(500).json({ error: err.message });
    }
});

app.get("/api/syncTags", authenticateToken, async (req, res) => {
    try {
        // await plcManager.disconnect(); // Disconnect any existing PLC first
        $tags = await plcManager.scanAllTags();
        console.log("Scan Tags:", $tags);
        res.json({ success: true, message: "PLC Sync Tags Done", tags: $tags });
    } catch (err) {
        console.error("❌ PLC sync tag error:", err.message);
        await sendToBackend({ type: "apiError", message: `PLC Sync API Error: ${err.message}` });
        res.status(500).json({ error: err.message });
    }
});

app.get("/api/test", async (req, res) => {
    res.json({ success: true, userId: wsServer.userId });
});

app.listen(config.api.port, () => {
    console.log("✅ HTTP API server listening on port :", config.api.port);
});
