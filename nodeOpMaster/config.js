const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '../.env') });

// console.log("✅ Loaded configuration from .env file");
// process.env.websocketPort
// console.log("✅ WebSocket Port:", process.env.websocketPort);

module.exports = {
    jwt: {
        JWT_SECRET: process.env["jwt.secretKey"],
        JWT_ALGORITHM: process.env["jwt.algorithm"],
    },
    ws: {
        port: process.env.websocketPort,
    },
    api: {
        port: process.env.nodejsApiPort,
        internalApiToken: process.env.internalApiToken,
    },
    plcConfig: {
        host: "",
        port: "",
        protocol: ""
    },
    alarmConfig: {},
    alarmStatus: {}, // To store active alarm states
    continuesReadTags: {},
    readLoopInterval: 100,
};
