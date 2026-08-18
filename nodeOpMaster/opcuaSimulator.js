const { OPCUAServer, Variant, DataType, StatusCodes, resolveNodeId } = require("node-opcua");

const server = new OPCUAServer({
    port: 4840,
    resourcePath: "/UA/Simulator",
    buildInfo: {
        productName: "OPC UA Dynamic Simulator",
        buildNumber: "1",
        buildDate: new Date()
    }
});

server.initialize(() => {
    console.log("OPC UA Server initialized.");
    const addressSpace = server.engine.addressSpace;

    const originalFindNode = addressSpace.findNode.bind(addressSpace);
    const dynamicValues = {};

    addressSpace.findNode = function(nodeId) {
        let node = originalFindNode(nodeId);
        
        let parsedNodeId = nodeId;
        if (typeof nodeId === 'string') {
            try { parsedNodeId = resolveNodeId(nodeId); } catch(e) {}
        }
        
        if (!node && parsedNodeId) {
            // IdentifierType.STRING is 2
            if (parsedNodeId.identifierType === 2 && parsedNodeId.namespace > 0) {
                const nsIndex = parsedNodeId.namespace;
                const nodeIdString = parsedNodeId.toString();
                
                // Ensure namespaces up to nsIndex exist
                let currentNsCount = addressSpace.getNamespaceArray().length;
                while (currentNsCount <= nsIndex) {
                    addressSpace.registerNamespace(`http://dynamic.namespace.sim/${currentNsCount}`);
                    currentNsCount++;
                }

                const ns = addressSpace.getNamespace(nsIndex);
                console.log(`[Simulator] Dynamically creating node: ${nodeIdString}`);
                
                try {
                    node = ns.addVariable({
                        componentOf: addressSpace.rootFolder.objects.server,
                        nodeId: nodeId,
                        browseName: nodeId.value.toString() || "DynamicVar",
                        dataType: "BaseDataType", 
                        value: {
                            get: function() {
                                if (dynamicValues[nodeIdString] !== undefined) {
                                    return new Variant(dynamicValues[nodeIdString]);
                                }
                                return new Variant({ dataType: DataType.String, value: "InitValue" });
                            },
                            set: function(variant) {
                                console.log(`[Simulator] Write to ${nodeIdString}:`, variant.value);
                                dynamicValues[nodeIdString] = { dataType: variant.dataType, value: variant.value };
                                return StatusCodes.Good;
                            }
                        }
                    });
                } catch (err) {
                    console.error(`[Simulator] Error creating node ${nodeIdString}:`, err);
                }
            }
        }
        return node;
    };
    
    const originalWrite = server.engine.write.bind(server.engine);
    server.engine.write = async function(context, nodesToWrite) {
        if (nodesToWrite && nodesToWrite.length) {
            for (const writeValue of nodesToWrite) {
                // Call findNode to trigger dynamic creation before original write processes it
                addressSpace.findNode(writeValue.nodeId);
            }
        }
        return originalWrite(context, nodesToWrite);
    };

    server.start(function() {
        console.log("==================================================");
        console.log(" OPC UA Dynamic Simulator is now listening...");
        console.log(" Endpoint: opc.tcp://localhost:" + server.endpoints[0].port);
        console.log("==================================================");
    });
});
