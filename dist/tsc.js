System.register("test-module", [], function (exports_1, context_1) {
    "use strict";
    var getResult;
    var __moduleName = context_1 && context_1.id;
    return {
        setters: [],
        execute: function () {
            getResult = (username, points) => {
                return `${username} scored ${points} points!`;
            };
            exports_1("getResult", getResult);
        }
    };
});
System.register("app", ["test-module"], function (exports_2, context_2) {
    "use strict";
    var myModule, title, result;
    var __moduleName = context_2 && context_2.id;
    return {
        setters: [
            function (myModule_1) {
                myModule = myModule_1;
            }
        ],
        execute: function () {
            title = "Hello";
            result = myModule.getResult;
            console.log(myModule.getResult);
        }
    };
});
//# sourceMappingURL=tsc.js.map