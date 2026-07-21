/************************************************/
/* PART 1: set custom chart options
/************************************************/

const myChartOptions = {
    title: {
        text: 'Monthly Revenue',
        left: 'center',
        top: 0,
    },
    legend: {
        // data: ['Revenue', 'Profit'],
        // top: 20,
    },
};

chartOptions["lineChart2"] = myChartOptions;

/************************************************/
/* PART 1 Ends:
/************************************************/


/************************************************/
/* PART 2: sample to call GET API
/************************************************/
jQuery(document).ready(function () {
    // apiCall('GET', "api/samples/sampleData").then(function (response) {
    //     console.log("Custom response: ", response);
    // }).catch(function (error) {
    //     console.log("Error: ", error.status);
    //     console.log("Error: ", error.statusText);
    // });
});

/************************************************/
/* PART 2 Ends:
/************************************************/


/************************************************/
/* PART 3: sample to call POST API with data submitted
/************************************************/
jQuery(document).ready(function () {

    // const data = {
    //     variable: 'value',
    //     variable2: 'value2',
    //     variable3: 'value3',
    // };

    // apiCall('POST', "api/samples/sampleData", data).then(function (response) {
    //     console.log("Custom response: ", response);
    // }).catch(function (error) {
    //     console.log("Error: ", error.status);
    //     console.log("Error: ", error.statusText);
    // });
});

/************************************************/
/* PART 3 Ends:
/************************************************/




// Function to register ready callbacks
// Register a callback to execute once all API calls are done
window.onApiReady(function () {
    // console.log("All API calls are completed. Now executing dependent logic.");
    // Your post-load logic here
});

// Another late subscription (Executes immediately if API is already ready)
setTimeout(() => {
    window.onApiReady(() => {
        // console.log("Late registered callback executed. 2");
    });
}, 2000);