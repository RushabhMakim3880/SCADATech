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

const endpointUrl = "opc.tcp://192.168.20.80:4840";
const client = OPCUAClient.create({ endpointMustExist: false });

(async () => {
    try {
        await client.connect(endpointUrl);
        const session = await client.createSession();

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
                const browseResult = await session.browse({
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
            const result = await session.browse(nodeId);
            for (const ref of result.references) {
                try {
                    if (ref.nodeClass === NodeClass.Variable) {
                        const dataTypeAttr = await session.read({
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

        await browseNode("ns=4;s=|var|LicOS-PAC-MC512.Application.GVL");
        console.log(JSON.stringify(tags, null, 2));
        await session.close();
        await client.disconnect();
    } catch (err) {
        console.error("Error:", err);
    }
})();
