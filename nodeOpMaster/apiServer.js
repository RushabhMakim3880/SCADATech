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

app.get("/api/systemInfo", authenticateToken, async (req, res) => {
    try {
        const os = require("os");
        const { execSync } = require("child_process");
        const path = require("path");

        // OS info
        const platform = os.platform();
        const osRelease = os.release();
        const hostname = os.hostname();
        const arch = os.arch();
        let osName = platform;
        try {
            osName = execSync("lsb_release -ds 2>/dev/null || cat /etc/os-release 2>/dev/null | grep PRETTY_NAME | cut -d= -f2 | tr -d '\"' || echo 'Unknown'", { encoding: "utf-8" }).trim();
        } catch (e) { }

        // Uptime
        const uptimeSec = os.uptime();
        const days = Math.floor(uptimeSec / 86400);
        const hours = Math.floor((uptimeSec % 86400) / 3600);
        const minutes = Math.floor((uptimeSec % 3600) / 60);
        const uptime = `${days}d ${hours}h ${minutes}m`;

        // CPU
        const cpus = os.cpus();
        const cpuModel = cpus[0] ? cpus[0].model : "Unknown";
        const cpuCores = cpus.length;
        let cpuUsage = 0;
        try {
            // get 1-second CPU usage snapshot
            const loadAvg = os.loadavg();
            cpuUsage = Math.min(100, (loadAvg[0] / cpuCores * 100)).toFixed(1);
        } catch (e) { }

        // RAM
        const totalMem = os.totalmem();
        const freeMem = os.freemem();
        const usedMem = totalMem - freeMem;
        const ramTotal = (totalMem / 1073741824).toFixed(2) + " GB";
        const ramUsed = (usedMem / 1073741824).toFixed(2) + " GB";
        const ramFree = (freeMem / 1073741824).toFixed(2) + " GB";
        const ramPercent = ((usedMem / totalMem) * 100).toFixed(1);

        // HDD
        let hddInfo = { total: "N/A", used: "N/A", free: "N/A", percent: "N/A" };
        try {
            const dfOutput = execSync("df -BG / | tail -1", { encoding: "utf-8" }).trim();
            const parts = dfOutput.split(/\s+/);
            hddInfo = {
                total: parts[1],
                used: parts[2],
                free: parts[3],
                percent: parts[4]
            };
        } catch (e) { }

        // Version control info
        const projectRoot = path.resolve(__dirname, "..");
        let branch = "N/A", lastCommit = "N/A", lastUpdated = "N/A", commitHash = "N/A", tagVersion = null;
        try {
            branch = execSync(`git -C "${projectRoot}" rev-parse --abbrev-ref HEAD 2>/dev/null`, { encoding: "utf-8" }).trim();
            lastCommit = execSync(`git -C "${projectRoot}" log -1 --pretty=format:"%s" 2>/dev/null`, { encoding: "utf-8" }).trim();
            commitHash = execSync(`git -C "${projectRoot}" log -1 --pretty=format:"%h" 2>/dev/null`, { encoding: "utf-8" }).trim();
            lastUpdated = execSync(`git -C "${projectRoot}" log -1 --pretty=format:"%ci" 2>/dev/null`, { encoding: "utf-8" }).trim();
        } catch (e) { }

        // Get latest git tag as version number (if any tags exist)
        try {
            tagVersion = execSync(`git -C "${projectRoot}" describe --tags --abbrev=0 2>/dev/null`, { encoding: "utf-8" }).trim();
        } catch (e) { tagVersion = null; }

        // Node.js version
        const nodeVersion = process.version;

        res.json({
            success: true,
            data: {
                os: { name: osName, release: osRelease, hostname, arch, platform },
                uptime,
                cpu: { model: cpuModel, cores: cpuCores, usage: cpuUsage + "%" },
                ram: { total: ramTotal, used: ramUsed, free: ramFree, usage: ramPercent + "%" },
                hdd: hddInfo,
                version: { branch, lastCommit, commitHash, lastUpdated, tagVersion },
                nodeVersion
            }
        });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.listen(config.api.port, () => {
    console.log("✅ HTTP API server listening on port :", config.api.port);
});
