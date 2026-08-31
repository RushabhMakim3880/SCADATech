const wsServer = require('./wsServer');
const { submitAlarmData } = require("./utils");

class AlarmManager {
    constructor() {
        this.alarmConfigs = [];
        this.activeStates = new Map(); // key: alarmId (e.g., 1:LO) => true/false
        this.configByTag = {}; // tagId => config object
    }

    loadConfig(configArray = []) {
        this.alarmConfigs = configArray;
        this.configByTag = {};

        for (const cfg of configArray) {
            const tagId = String(cfg.tagId);
            this.configByTag[tagId] = cfg;
        }

        console.log(`[AlarmManager] Loaded alarm configs for ${Object.keys(this.configByTag).length} tags`);
    }

    loadActiveStates(stateObj = {}) {
        for (const [alarmId, isActive] of Object.entries(stateObj)) {
            this.activeStates.set(alarmId, isActive);
        }
        console.log(`[AlarmManager] Restored ${this.activeStates.size} active alarm states`);
    }

    getActiveStates() {
        const result = {};
        for (const [alarmId, state] of this.activeStates.entries()) {
            result[alarmId] = state;
        }
        return result;
    }

    processBatch(tagMap = {}) {
        for (const [tagId, cfg] of Object.entries(this.configByTag)) {
            let rawVal = tagMap[tagId];
            if (rawVal === undefined) continue;

            // Handle boolean values: false -> 0, true -> 1
            if (typeof rawVal === "boolean") {
                rawVal = rawVal ? 1 : 0;
            }

            const value = parseFloat(rawVal);
            if (isNaN(value)) continue;

            const levels = this.evaluateLevels(value, cfg);
            const allLevelKeys = ['lolo', 'lo', 'hi', 'hihi'];

            for (const level of allLevelKeys) {
                const alarmId = `${cfg.alarmId}:${level}`;
                const isActive = this.activeStates.get(alarmId) === true;
                const matched = levels && levels.level === level;

                if (matched && !isActive) {
                    this.activeStates.set(alarmId, true);
                    this.triggerAlarm(alarmId, cfg, level, value);
                } else if (!matched && isActive) {
                    this.activeStates.set(alarmId, false);
                    this.resolveAlarm(alarmId, cfg, level, value);
                }
            }
        }
    }

    evaluateLevels(value, cfg) {
        const t = {
            lolo: parseFloat(cfg.loloTheresold),
            lo: parseFloat(cfg.loTheresold),
            hi: parseFloat(cfg.hiTheresold),
            hihi: parseFloat(cfg.hihiTheresold)
        };

        if (!isNaN(t.hihi) && value >= t.hihi)
            return { level: 'hihi', type: 'critical', message: 'Very High' };
        if (!isNaN(t.hi) && value >= t.hi)
            return { level: 'hi', type: 'warning', message: 'High' };
        if (!isNaN(t.lolo) && value <= t.lolo)
            return { level: 'lolo', type: 'critical', message: 'Very Low' };
        if (!isNaN(t.lo) && value <= t.lo)
            return { level: 'lo', type: 'warning', message: 'Low' };

        return null;
    }

    triggerAlarm(alarmId, cfg, level, value) {
        const type = ['lolo', 'hihi'].includes(level) ? 'critical' : 'warning';
        let message;
        if (cfg.message && cfg.message.trim() !== "") {
            message = `${cfg.message}`;
        } else {
            message = `${cfg.tagName}: ${level.toUpperCase()} alarm`;
        }
        console.log(`[Alarm] Triggered: ${type.toUpperCase()} | ${message}`);

        const alarmData = {
            type: 'alarm',
            action: 'trigger',
            alarmId,
            tagId: cfg.tagId,
            tagName: cfg.tagName,
            message,
            alarmType: type,
            value
        };

        submitAlarmData(alarmData);
        wsServer.broadcast(alarmData);
    }

    resolveAlarm(alarmId, cfg, level, value) {
        const type = ['lolo', 'hihi'].includes(level) ? 'critical' : 'warning';

        let message;
        if (cfg.message && cfg.message.trim() !== "") {
            message = `Resolved: ${cfg.message}`;
        } else {
            message = `${cfg.tagName}: ${level.toUpperCase()} alarm resolved`;
        }

        console.log(`[Alarm] Resolved: ${type.toUpperCase()} | ${message}`);

        const alarmData = {
            type: 'alarm',
            action: 'resolve',
            alarmId,
            tagId: cfg.tagId,
            tagName: cfg.tagName,
            message,
            alarmType: type,
            value
        };

        submitAlarmData(alarmData);
        wsServer.broadcast(alarmData);
    }
}

// Singleton
module.exports = new AlarmManager();
