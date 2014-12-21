/**
 * Cookie tools
 *
 * @namespace APP
 * @class cookie
 */

APP.namespace.create('cookie');
APP.cookie = (function ($, _) {

    var settings = {

    };

    function setCookie(name, value, exp_days) {

        var exp_days = typeof exp_days !== 'undefined' ? exp_days : 7,
            ex_date  = new Date(),
            c_value;

        ex_date.setDate(ex_date.getDate() + exp_days);
        c_value = _.escape(value) + (!exp_days ? "" : "; expires=" + ex_date.toUTCString());

        document.cookie = name + "=" + c_value;
    }

    function getCookie(name) {

        var cookies = document.cookie.split(';'),
            match,
            i;

        for (i = 0; i < cookies.length; i += 1) {

            match = cookies[i].substr(0, cookies[i].indexOf('='));
            match = match.replace(/^\s+|\s+$/g, '');

            if (match === name) {

                return _.unescape(cookies[i].substr(cookies[i].indexOf('=') + 1));
            }
        }

        return false;
    }

    function checkCookie(name, value) {

        if (getCookie(name) == value) {

            return true;
        }

        return false;
    }

    return {
        'set'   : setCookie,
        'get'   : getCookie,
        'check' : checkCookie
    };

}(jQuery, _));



