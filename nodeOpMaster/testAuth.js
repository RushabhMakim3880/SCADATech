const { OPCUAClient, UserTokenType } = require("node-opcua");
const endpointUrl = "opc.tcp://192.168.20.80:4840";

(async () => {
    const c = OPCUAClient.create({ endpointMustExist: false });
    await c.connect(endpointUrl);
    const eps = await c.getEndpoints();
    await c.disconnect();
    for (const e of eps) {
        console.log("==", e.endpointUrl, e.securityPolicyUri, e.securityMode);
        console.log(" tokens:", (e.userIdentityTokens || []).map(t => t.tokenType)); // expect to see UserName here
    }
})();
