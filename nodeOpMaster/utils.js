const axios = require("axios");
const config = require("./config");
// const alarmManager = require('./alarmManager');

// Disable SSL certificate verification for development purposes
process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

async function sendToBackend(payload) {
    try {
        const apiUrl = process.env["nodeBaseUrl"] + "api/OpMasterBack/submitData";
        const response = await axios.post(apiUrl,
            payload,
            {
                headers: {
                    'X-Internal-Token': config.api.internalApiToken,
                    'Content-Type': 'application/json',
                }
            }
        );
        return response.data; // ✅ return API response data
    } catch (err) {
        console.error("Failed to send error to backend:", err.message);
        throw err; // ✅ optionally rethrow so caller can handle
    }
}

// write function to fetch data for config from backend
// This function can be used to fetch configuration data from the backend if needed
async function fetchConfigFromBackend() {
    try {
        const configUrl = process.env["nodeBaseUrl"] + "api/OpMasterBack/getConfig";
        const response = await axios.get(configUrl, {
            headers: {
                'X-Internal-Token': config.api.internalApiToken,
                'Content-Type': 'application/json',
            }
        });

        config.plcConfig = response.data.data.plcConfig; // ✅ update local config with PLC config
        config.continuesReadTags = response.data.data.continuesReadTags || {};
        config.readLoopInterval = response.data.data.readLoopInterval || 100;
        config.alarmConfig = response.data.data.alarmConfig || {};
        config.alarmStatus = response.data.data.alarmStatus || {}; // ✅ update local config with alarm status

        // // Load alarm config and optionally restore active states
        // alarmManager.loadConfig(response.data.data.alarmConfig);
        // if (config.alarmStatus) {
        //     alarmManager.loadActiveStates(response.data.data.alarmStatus);
        // }

        console.log("✅ Fetched PLC configuration from backend:", config.plcConfig);
        console.log("✅ Fetched Continues Read Tags from backend:", Object.keys(config.continuesReadTags).length);
        console.log("✅ Fetched Read Loop Interval from backend:", config.readLoopInterval);
        console.log("✅ Fetched Alarm Config from backend:", response.data.data.alarmConfig);

        // Optionally update other config values if needed
        return true; // ✅ return API response data
    } catch (err) {
        console.error("Failed to fetch config from backend:", err.message);
        throw err; // ✅ optionally rethrow so caller can handle
    }
}

// write function to submit alarm data to backend
async function submitAlarmData(alarmData) {
    try {
        const apiUrl = process.env["nodeBaseUrl"] + "api/OpMasterBack/submitAlarmData";
        const response = await axios.post(apiUrl,
            alarmData,
            {
                headers: {
                    'X-Internal-Token': config.api.internalApiToken,
                    'Content-Type': 'application/json',
                }
            }
        );
        return response.data; // ✅ return API response data
    } catch (err) {
        console.error("Failed to submit alarm data to backend:", err.message);
        throw err; // ✅ optionally rethrow so caller can handle
    }
}

module.exports = { sendToBackend, fetchConfigFromBackend, submitAlarmData };
