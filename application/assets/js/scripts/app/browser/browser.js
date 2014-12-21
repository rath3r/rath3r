/**
 * Provides information about used browser
 *
 * @namespace APP
 * @class browser
 */

APP.namespace.create('browser');
APP.browser = (function (_) {

        /**
         * Object containing settings for browsers
         * 
         * @property _browsers
         * @private
         * @type {Object}
         */

    var _browsers = [
            {
                /**
                 * Is current browser Google Chrome
                 * 
                 * @property chrome
                 * @type {Boolean}
                 */

                search   : /Chrome/,
                identity : 'Google Chrome',
                name     : 'chrome',
                version  : 'Chrome'
            }, {

                /**
                 * Is current browser Safari
                 * 
                 * @property safari
                 * @type {Boolean}
                 */

                search   : /Safari/,
                identity : 'Safari',
                name     : 'safari',
                version  : 'Version'
            }, {

                /**
                 * Is current browser Opera
                 * 
                 * @property opera
                 * @type {Boolean}
                 */

                search   : /Opera/,
                identity : 'Opera',
                name     : 'opera',
                version  : 'Version'
            }, {

                /**
                 * Is current browser Mozilla Firefox
                 * 
                 * @property firefox
                 * @type {Boolean}
                 */

                search   : /Firefox/,
                identity : 'Mozilla Firefox',
                name     : 'firefox',
                version  : 'Firefox'
            }, {

                /**
                 * Is current browser Internet Explorer
                 * 
                 * @property ie
                 * @type {Boolean}
                 */

                search   : /MSIE/,
                identity : 'Internet Explorer',
                name     : 'ie',
                version  : 'MSIE'
            }, {

                /**
                 * Is current browser Mozilla
                 * 
                 * @property mozilla
                 * @type {Boolean}
                 */

                search   : /Gecko/,
                identity : 'Mozilla',
                name     : 'mozilla',
                version  : 'rv'
            }],

        /**
         * Object containing settings for operation systems
         * 
         * @property _systems
         * @private
         * @type {Object}
         */

        _systems = [
            {
                /**
                 * Is current operation system Microsoft Windows
                 * 
                 * @property win
                 * @type {Boolean}
                 */

                search   : /Win/,
                identity : 'Microsoft Windows',
                name     : 'win'
            }, {

                /**
                 * Is current operation system Apple Mac
                 * 
                 * @property mac
                 * @type {Boolean}
                 */

                search   : /Mac/,
                identity : 'Apple Mac',
                name     : 'mac'
            }, {

                /**
                 * Is current operation system Apple iOS
                 * 
                 * @property ios
                 * @type {Boolean}
                 */

                search   : /(iPhone|iPad|iPod)/,
                identity : 'Apple iOS',
                name     : 'ios'
            }, {

                /**
                 * Is current operation system Linux
                 * 
                 * @property linux
                 * @type {Boolean}
                 */

                search   : /Linux/,
                identity : 'Linux',
                name     : 'linux'
            }],

        /**
         * Object containing settings for other browser features
         * 
         * @property _features
         * @private
         * @type {Object}
         */

        _features = [
            {
                /**
                 * Is current browser using WebKit
                 * 
                 * @property webkit
                 * @type {Boolean}
                 */

                search   : /WebKit/,
                identity : 'AppleWebKit',
                name     : 'webkit'
            }, {

                /**
                 * Is current device an iPhone (iPod Touch)
                 * 
                 * @property iphone
                 * @type {Boolean}
                 */

                search   : /(iPhone|iPod)/,
                identity : 'iPhone',
                name     : 'iphone'
            }, {

                /**
                 * Is current device an iPad
                 * 
                 * @property ipad
                 * @type {Boolean}
                 */

                search   : /iPad/,
                identity : 'iPad',
                name     : 'ipad'
            }, {

                /**
                 * Is current device an mobile device
                 * 
                 * @property mobile
                 * @type {Boolean}
                 */

                search   : /Mobile/,
                identity : 'Mobile Device',
                name     : 'mobile'
            }, {

                /**
                 * Is current device a BlackBerry device
                 *
                 * @property blackberry
                 * @type {Boolean}
                 */

                search   : /BlackBerry/,
                identity : 'BlackBerry',
                name     : 'blackberry'
            }, {

                /**
                 * Is current device an Android device
                 *
                 * @property android
                 * @type {Boolean}
                 */

                search   : /Android/,
                identity : 'Android',
                name     : 'android'
            }],

        /**
         * Variable used for storing search filter to get version
         * 
         * @property _version_search_string
         * @private
         * @type {String}
         */

        _version_search_string,

        /**
         * Object for storing additional browser information
         * 
         * @property _info
         * @private
         * @type {Object}
         */

        _info = {},

        /**
         * Object with results of browser tests
         * 
         * @property _results
         * @private
         * @type {Object}
         */

        _results = {};


    /**
     * Adds a new variable into the 'results' object.
     *
     * @method _addToResults
     * @private
     * @param {String} value_name Name of the variable
     * @param {Boolean|String} value Value of the variable
     * @return {Number} Version as an whole number
     */

    function _addToResults(value_name, value) {

        _results[value_name] = value;
    }


    /**
     * Search and test Object.
     * Saves also results into 'results' object.
     * Sets '_version_search_string' variable for further searching.
     *
     * @method _searchString
     * @private
     * @param {String} agent String which is used for matching
     * @param {Object} data Object with test settings
     * @param {Boolean} one If set it will return onaly one true result
     * @return {Object} Object with test results
     */

    function _searchString(agent, data, one) {

        var found = false,
            one_result = typeof one !== 'undefined' || false,
            test,
            first = false,
            i;

        for (i = 0; i < data.length; i += 1) {

            test = false;

            if (agent.match(data[i].search) && !first) {

                _version_search_string = data[i].version || false;
                found = data[i].identity;
                test  = true;

                if (one_result) {
                    first = true;
                }
            }

            _addToResults(data[i].name, test);
        }

        return found;
    }


    /**
     * Adds a new variable into the 'results' object.
     *
     * @method _searchVersion
     * @private
     * @param {String} data_string String which is used for searching
     * @return {Boolean|Number} Version as an whole number
     */

    function _searchVersion(data_string) {

        var index;

        if (_version_search_string) {

            index = data_string.indexOf(_version_search_string);

            if (index === -1) {
                return false;
            }

            return parseFloat(data_string.substring(index + _version_search_string.length + 1));
        }

        return false;
	}


    /**
     * Test for browser and version match
     *
     * @method _isBrowserAndVersion
     * @private
     * @param {Boolean} browser Browser variable from 'results' object
     * @param {Number|Boolean} version Browser version from 'results' object
     * @param {Number} target_version Target version for comparison
     * @return {Boolean} Returns true if success, false if not.
     */

    function _isBrowserAndVersion(browser, version, target_version) {

        if (browser && version === target_version) {

            return true;
        }

        return false;
    }


    /**
     * Test for any handheld device
     *
     * @method _isHandheld
     * @private
     * @return {Boolean} Returns true if success, false if not.
     */

    function _isHandheld() {

        var agents = [
                'Android',
                'webOS',
                'iPhone',
                'iPpad',
                'BlackBerry',
                'iPod',
                'Mobile',
                'ZuneWP7',
                'Windows Phone',
                'IEMobile',
                'Opera Mini'],
            i;

        for (i in agents) {

            if (navigator.userAgent.match(new RegExp(agents[i], 'i'))) {
                return true;
            }
        }

        return false;
    }


    /**
     * Test browser and return results
     *
     * @method _initialise
     * @private
     * @return {Object} Returns results of tests as an Object.
     */

    function _initialise() {

        _info = {

            /**
             * Name of the current browser agent
             * 
             * @property name
             * @type {String}
             */

            name    : _searchString(navigator.userAgent, _browsers, true) || 'unknown',

            /**
             * Version of the current browser agent
             * 
             * @property version
             * @type {Number}
             */

            version : _searchVersion(navigator.userAgent) || _searchVersion(navigator.appVersion) || 'unknown',

            /**
             * Name of the current operation system 
             * 
             * @property os
             * @type {String}
             */

            os      : _searchString(navigator.platform, _systems, true) || 'unknown'
        };

        _searchString(navigator.userAgent, _features);


        /**
         * Is the current browser Internet Explorer 7
         * 
         * @property ie7
         * @type {Boolean}
         */

        _info.ie7 = _isBrowserAndVersion(_results.ie, _info.version, 7);

        /**
         * Is the current browser Internet Explorer 8
         * 
         * @property ie8
         * @type {Boolean}
         */

        _info.ie8 = _isBrowserAndVersion(_results.ie, _info.version, 8);

        /**
         * Is the current browser Internet Explorer 9
         * 
         * @property ie9
         * @type {Boolean}
         */

        _info.ie9 = _isBrowserAndVersion(_results.ie, _info.version, 9);

        /**
         * Is the current devide handheld device
         *
         * @property handheld
         * @type {Boolean}
         */

        _info.handheld = _isHandheld();

        return _.extend(_info, _results);
    }


    return _initialise();

}(_));



