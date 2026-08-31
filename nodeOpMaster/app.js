require("./apiServer");
const { fetchConfigFromBackend } = require("./utils");
const plcManager = require("./plcManager");
const config = require("./config");
const wsServer = require("./wsServer");
const alarmManager = require('./alarmManager');

async function initializeApp() {
    console.log("Initializing NodeOpMaster...");
    const configFetched = await fetchConfigFromBackend();
    if (configFetched) {

        // Load alarm config and optionally restore active states
        alarmManager.loadConfig(config.alarmConfig);
        if (config.alarmStatus) {
            alarmManager.loadActiveStates(config.alarmStatus);
        }


        // Initialize PLC Manager with fetched config
        await plcManager.disconnect(); // Disconnect any existing PLC first
        await plcManager.init(config.plcConfig, wsServer);
        console.log("✅ PLC Manager initialized with config:", config.plcConfig);
        console.log("✅ Continues Read Tags:", Object.keys(config.continuesReadTags).length);
        console.log("✅ Read Loop Interval:", config.readLoopInterval);


        // Start the read loop if configured
        if (Object.keys(config.continuesReadTags).length > 0) {
            plcManager.setReadTagsToPoll(config.continuesReadTags);
            // plcManager.startReadLoop();
            console.log("✅ Read loop started with tags:", Object.keys(config.continuesReadTags).length);
        } else {
            console.log("✅ No continuous read tags configured.");
        }

        console.log("✅ App started — WS and API servers running");
    }
}

initializeApp();
