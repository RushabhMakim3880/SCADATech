const {
    NodeClass,
    OPCUAClient,
    AttributeIds,
    DataType,
    StatusCodes,
    TimestampsToReturn,
} = require("node-opcua");

const OPCUABuiltInDataTypes = {
    "ns=0;i=1": "Boolean",
    "ns=0;i=2": "SByte",
    "ns=0;i=3": "Byte",
    "ns=0;i=4": "Int16",
    "ns=0;i=5": "UInt16",
    "ns=0;i=6": "Int32",
    "ns=0;i=7": "UInt32",
    "ns=0;i=8": "Int64",
    "ns=0;i=9": "UInt64",
    "ns=0;i=10": "Float",
    "ns=0;i=11": "Double",
    "ns=0;i=12": "String",
    "ns=0;i=13": "DateTime",
    "ns=0;i=14": "Guid",
    "ns=0;i=15": "ByteString",
    "ns=0;i=16": "XmlElement",
    "ns=0;i=17": "NodeId",
    "ns=0;i=18": "ExpandedNodeId",
    "ns=0;i=19": "StatusCode",
    "ns=0;i=20": "QualifiedName",
    "ns=0;i=21": "LocalizedText",
    "ns=0;i=22": "Structure",
    "ns=0;i=23": "DataValue",
    "ns=0;i=24": "Variant",
    "ns=0;i=25": "DiagnosticInfo",
};



class OPCUAService {
    constructor() {
        this.client = null;
        this.session = null;
        this.wsServer = null; // Will be set from outside
        this.isConnected = false;
        this.isReconnecting = false;
        this.autoReconnect = true;
        this.reconnectInterval = 5000;
        this.endpointUrl = null;
    }

    setWsServer(ws) {
        this.wsServer = ws;
    }

    async connect(endpointUrl) {
        this.endpointUrl = endpointUrl;
        this.client = OPCUAClient.create({
            endpointMustExist: false,
            keepSessionAlive: true,
            connectionStrategy: {
                initialDelay: 1000,
                maxRetry: 3,
            },
        });

        this._registerEvents();

        try {
            await this.client.connect(endpointUrl);
            this.session = await this.client.createSession();
            this.isConnected = true;

            console.log("✅ OPC UA connected to", endpointUrl);
            this._notifyWs({ type: "plcStatus", status: "connected", message: "PLC connected" });
        } catch (err) {
            this.isConnected = false;
            console.error("❌ OPC UA connect error:", err.message);
            this._notifyWs({ type: "plcStatus", status: "error", message: `Connect error: ${err.message}` });
            throw err;
        }
    }

    async disconnect() {
        try {
            if (this.session) await this.session.close();
            if (this.client) await this.client.disconnect();
            this.isConnected = false;

            console.log("✅ Disconnected from PLC");
            this._notifyWs({ type: "plcStatus", status: "disconnected", message: "PLC disconnected" });
        } catch (err) {
            console.error("❌ Disconnect error:", err.message);
            this._notifyWs({ type: "plcStatus", status: "error", message: `Disconnect error: ${err.message}` });
        }
    }

    async readTags(nodeMap) {
        const tagIds = Object.keys(nodeMap);
        const nodesToRead = tagIds.map(tagId => ({
            nodeId: nodeMap[tagId],
            attributeId: AttributeIds.Value,
        }));

        // console.log("Reading tags:", nodesToRead);

        const dataValues = await this.session.read(nodesToRead);
        // console.log("Read Values:", dataValues);
        const result = {};
        tagIds.forEach((tagId, i) => {
            let value = dataValues[i].value.value;

            // if value is a number, limit to 3 decimal places
            if (typeof value === "number") {
                value = parseFloat(value.toFixed(3));
            }

            result[tagId] = value;
        });

        this._notifyWs({ type: "tagValues", status: "read", message: "Tags read successfully", data: result });
        return result;
    }


    async writeTags(tagsObj) {
        const nodesToWrite = Object.entries(tagsObj).map(([nodeId, tagData]) => {
            const { value, dataType } = tagData;
            return {
                nodeId,
                attributeId: AttributeIds.Value,
                value: {
                    value: {
                        dataType: DataType[dataType] ?? this.guessDataType(value),
                        value,
                    },
                },
            };
        });

        const statusCodes = await this.session.write(nodesToWrite);
        const success = statusCodes.every(sc => sc === StatusCodes.Good);

        if (!success) {
            // this._notifyWs({ type: "plcStatus", status: "error", message: "Some tags failed to write" });
            throw new Error("Some tags failed to write");
        } else {
            // this._notifyWs({ type: "plcStatus", status: "write", message: "Tags written successfully" });
        }

        return success;
    }

    guessDataType(value) {
        if (typeof value === "boolean") return DataType.Boolean;
        if (typeof value === "number" && Number.isInteger(value)) return DataType.Int32;
        if (typeof value === "number") return DataType.Float;
        return DataType.String;
    }

    _registerEvents() {
        this.client.on("connected", () => {
            console.log("✅ Event: Connected");
            this._notifyWs({ type: "plcStatus", status: "connected", message: "PLC connected (event)" });
        });

        this.client.on("connection_lost", async () => {
            console.log("❌ Event: Connection lost");
            this.isConnected = false;
            this._notifyWs({ type: "plcStatus", status: "lost", message: "PLC connection lost" });
            // if (this.autoReconnect) await this._tryReconnect();
        });

        this.client.on("after_reconnection", () => {
            console.log("✅ Event: Reconnected successfully");
            this.isConnected = true;
            this.isReconnecting = false;
            this._notifyWs({ type: "plcStatus", status: "connected", message: "PLC reconnected" });
        });

        this.client.on("reconnection_attempt_has_failed", () => {
            console.log("❌ Event: Reconnection permanently failed");
            this._notifyWs({ type: "plcStatus", status: "error", message: "Reconnection permanently failed" });
        });

        this.client.on("timed_out_request", (request) => {
            console.log("⚠️ Event: Timed out request:", request.toString());
            this._notifyWs({ type: "plcStatus", status: "timeout", message: "PLC request timed out" });
        });

        this.client.on("close", () => {
            console.log("⚠️ Event: Connection closed");
            this.isConnected = false;
            this._notifyWs({ type: "plcStatus", status: "disconnected", message: "PLC connection closed" });
        });
    }

    async _tryReconnect() {
        if (this.isReconnecting) return;
        this.isReconnecting = true;

        while (!this.isConnected && this.autoReconnect) {
            try {
                console.log("🔄 Reconnecting...");
                this._notifyWs({ type: "plcStatus", status: "reconnecting", message: "Reconnecting to PLC..." });
                await this.connect(this.endpointUrl);
                console.log("✅ Reconnected!");
                this.isReconnecting = false;
                break;
            } catch (err) {
                console.log("Reconnect failed, retrying in", this.reconnectInterval, "ms");
                this._notifyWs({ type: "plcStatus", status: "error", message: `Reconnect failed: ${err.message}` });
                await new Promise(res => setTimeout(res, this.reconnectInterval));
            }
        }
    }

    _notifyWs(data) {
        if (this.wsServer) {
            this.wsServer.broadcast(data);
        }
    }

    async scanAllTags(startNode = "ns=4;s=|var|LicOS-PAC-MC512.Application.GVL") {
        const tags = [];

        const typeCache = {};

        const resolveBuiltInDataType = async (dataTypeNodeId) => {
            const idStr = dataTypeNodeId.toString();

            if (typeCache[idStr]) return typeCache[idStr];
            if (OPCUABuiltInDataTypes[idStr]) {
                typeCache[idStr] = OPCUABuiltInDataTypes[idStr];
                return typeCache[idStr];
            }

            try {
                const browseResult = await this.session.browse({
                    nodeId: dataTypeNodeId,
                    referenceTypeId: "HasSubtype",
                    includeSubtypes: true,
                    browseDirection: 2, // Inverse
                });

                if (browseResult.references) {
                    for (const ref of browseResult.references) {
                        const resolved = await resolveBuiltInDataType(ref.nodeId);
                        if (resolved !== "Unknown") {
                            typeCache[idStr] = resolved;
                            return resolved;
                        }
                    }
                }
            } catch (err) {
                console.warn(`⚠️ Failed to resolve base type of ${idStr}:`, err.message);
            }

            typeCache[idStr] = "Unknown";
            return "Unknown";
        };


        const browseNode = async (nodeId) => {
            const result = await this.session.browse(nodeId);
            for (const ref of result.references) {
                try {
                    if (ref.nodeClass === NodeClass.Variable) {
                        const dataTypeAttr = await this.session.read({
                            nodeId: ref.nodeId,
                            attributeId: AttributeIds.DataType
                        });

                        const dataTypeNodeId = dataTypeAttr.value.value;
                        const resolvedType = await resolveBuiltInDataType(dataTypeNodeId);
                        const dataTypeStr = resolvedType || dataTypeNodeId.toString();

                        tags.push({
                            tagAddress: ref.nodeId.toString(),
                            tagName: ref.browseName.name,
                            dataType: dataTypeStr,
                        });
                    }

                    if (ref.nodeClass === NodeClass.Object || ref.nodeClass === NodeClass.Folder) {
                        await browseNode(ref.nodeId);
                    }
                } catch (err) {
                    console.error(`Failed to read/browse ${ref.browseName.name}:`, err.message);
                }
            }
        };

        try {
            await browseNode(startNode);
            this._notifyWs({ type: "plcStatus", status: "scan", message: "Tag scan completed", data: tags });
            return tags;
        } catch (err) {
            console.error("❌ Scan error:", err.message);
            this._notifyWs({ type: "plcStatus", status: "error", message: `Scan error: ${err.message}` });
            throw err;
        }
    }


}

module.exports = OPCUAService;
