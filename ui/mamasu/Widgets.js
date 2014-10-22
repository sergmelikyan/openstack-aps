define(["dojo/Deferred", "aps/Memory", "aps/load"], function (Deferred, Memory, load) {
    return {
        /**
         * 
         * @param {objects array} data: contains an array of objects with the same structure.
         * @param {string} where: id of item where do you want to show GridInspector
         * @returns {Widgets_L1.Deferred}
         */
        ObjectInspector: function (data, where) {
            var def = new Deferred();
            console.log("Object Inspector", data);

            var mem = new Memory({
                data: data
            });
            var layout = [];
            for (var key in data[0]) {
                layout[layout.length] = {
                    name: key,
                    field: key
                };
            }
            load(["aps/Grid", {
                    store: mem,
                    columns: layout
                }], where).then(def.resolve).otherwise(def.reject);
            
            return def;
        }
    };
});