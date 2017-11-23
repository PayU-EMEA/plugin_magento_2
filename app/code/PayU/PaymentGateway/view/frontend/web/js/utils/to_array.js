define([], function () {
    return function (data) {
        if (Array.isArray(data)) {
            return data;

        } else if (typeof data === 'object' && data !== null) {
            return Object.keys(data).map(function (key) {
                return data[key];
            });
        }

        return [];
    };
});
