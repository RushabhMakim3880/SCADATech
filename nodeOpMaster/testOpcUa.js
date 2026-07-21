const {
    OPCUAClient,
    AttributeIds,
    DataType,
    StatusCodes
} = require("node-opcua");

const client = OPCUAClient.create({
    endpointMustExist: false,
});

const endpointUrl = "opc.tcp://192.168.20.80:4840";

(async () => {
    try {
        await client.connect(endpointUrl);
        const session = await client.createSession();


        const writeValue = {
            nodeId: "ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.B_VELOCITY", // your writable tag
            attributeId: AttributeIds.Value,
            value: {
                value: {
                    dataType: DataType.Double,  // Use correct type (e.g., Boolean, Int16, Float, etc.)
                    value: 11.22               // New value to write
                }
            }
        };

        const statusCode = await session.write(writeValue);
        if (statusCode === StatusCodes.Good) {
            console.log("✅ Value written successfully");
        } else {
            console.log("❌ Write failed:", statusCode.toString());
        }

        const nodesToRead = [
            {
                nodeId: "ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.A_RESET",
                attributeId: AttributeIds.Value,
            },
            {
                nodeId: "ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.A_VELOCITY",
                attributeId: AttributeIds.Value,
            }
        ];

        const dataValues = await session.read(nodesToRead);

        console.log("Read Values:", dataValues);

        dataValues.forEach((val, i) => {
            console.log(`Tag ${i + 1} Value:`, val.value.value);
        });

        await session.close();
        await client.disconnect();
    } catch (err) {
        console.error("OPC UA Error:", err.message);
    }
})();
