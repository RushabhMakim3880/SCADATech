const OPCUAClient = require("./opcuaClient");
const { Mutex } = require("async-mutex");
const config = require("./config");
const alarmManager = require('./alarmManager');

class PLCManager {
    constructor() {
        this.client = null;
        this.config = null;
        this.protocol = null;

        this.isInitialized = false;
        this.isConnected = false;
        this.isReconnecting = false;
        this.lastError = null;
        this.lastTagsRead = {};
        this.lastTagsWritten = {};

        this.mutex = new Mutex();

        this.readLoopActive = false;
        this.readTagsToPoll = {};
        this.readLoopInterval = config.readLoopInterval; // Default read interval
    }

    // function to set readTagsToPoll
    setReadTagsToPoll(tags) {
        this.readTagsToPoll = tags;
    }

    async init(config, wsServer) {
        console.log("Initializing PLCManager with config:", config);
        try {
            this.config = config;
            this.protocol = config.protocol;
            this.isInitialized = true;

            if (this.protocol === "opc-ua") {
                this.client = new OPCUAClient();
                this.client.setWsServer(wsServer);

                const endpointUrl = `opc.tcp://${config.host}:${config.port}`;
                await this.client.connect(endpointUrl);

                this.isConnected = this.client.isConnected;
                this.isReconnecting = this.client.isReconnecting;
                this.lastError = this.client.lastError;

                // Start read loop if configured
                this.startReadLoop();
            }
            // Future: else if (modbus) {...}

        } catch (err) {
            this.lastError = err.message;
            this.isConnected = false;
            throw err;
        }
    }

    async disconnect() {
        if (this.client) {
            await this.client.disconnect();
            this.client = null;
            this.config = null;
            this.isConnected = false;
            this.isInitialized = false;
        }
    }

    async readTags(tagIds) {
        return await this.mutex.runExclusive(async () => {
            if (!this.client || !this.isConnected) {
                throw new Error("PLC is not connected");
            }
            const data = await this.client.readTags(tagIds);
            alarmManager.processBatch(data);
            this.lastTagsRead = data;
            return data;
        });
    }

    async writeTags(tagsMap) {
        return await this.mutex.runExclusive(async () => {
            if (!this.client || !this.isConnected) {
                throw new Error("PLC is not connected");
            }
            const success = await this.client.writeTags(tagsMap);
            this.lastTagsWritten = tagsMap;
            return success;
        });
    }

    async scanAllTags() {
        return await this.mutex.runExclusive(async () => {
            if (!this.client || !this.isConnected) {
                throw new Error("PLC is not connected");
            }
            return await this.client.scanAllTags();
        });
    }

    startReadLoop() {
        if (this.readLoopActive) return;
        this.readLoopActive = true;
        this._readLoop();
    }

    stopReadLoop() {
        this.readLoopActive = false;
    }

    async _readLoop() {
        while (this.readLoopActive) {
            try {
                const data = await this.readTagsInBatches(this.readTagsToPoll, 32);
                // You can optionally forward to WS here
            } catch (err) {
                console.error("PLCManager read loop error:", err.message);
            }
            await new Promise(res => setTimeout(res, this.readLoopInterval));
        }
    }

    async readTagsInBatches(tagMap, batchSize = 50) {
        const tagIds = Object.keys(tagMap);
        const results = {};

        for (let i = 0; i < tagIds.length; i += batchSize) {
            const chunk = tagIds.slice(i, i + batchSize);
            const nodeMap = Object.fromEntries(chunk.map(tagId => [tagId, tagMap[tagId]]));
            const partial = await this.readTags(nodeMap);
            Object.assign(results, partial);
        }

        return results;
    }
}

module.exports = new PLCManager();
