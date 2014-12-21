
================================================================================
 JS ASSET - APP (module pattern) + plugins
================================================================================


System requirements:
--------------------

    For minification - Java Enviroment
    (YUI Compressor)



Working directories:
--------------------

    1. 'plugins' folder:

        - js. files in this directory are compiled only
            * insert paths to partials files to compile
            * plugins should be added already minifed because they are not
              minifed in deployment they are just joined together
            * each line = one file
            * no empty lines, no tabs or any whitespace characters
            * example:
                libs/safe-console-log.js
                libs/lodash.min.js

        - partial files in sub-directories (for example: libs folder)


    2. 'scripts' folder:

        - js. files in this directory are compiled only
            * insert paths to partials files to compile
            * 'app/APP.js' has to be a first file always in every package
            * each line = one file
            * no empty lines, no tabs or any whitespace characters
            * example:
                app/APP.js
                app/browser/browser.js
                app/functions/rand.js

        - APP is using Lo-Dash (Underscore) lib
            * jQuery - hard dependency for APP - loaded from CDN
            * hard dependency for APP - libs/lodash.min.js (in plugins)
            * http://lodash.com/

    3. plugins must be loaded before APP, because of the dependencies (jQuery, Lo-Dash, Modernizr)


Module pattern:
---------------

    - Creating new Namespace modules:

        APP.namespace.create('newPackage.mySubPackage.subSubPackage');
        APP.newPackage.mySubPackage.subSubPackage = (function () { ... }());


    - New module example:

        // creating new namespace in APP object
        APP.namespace.create('example.visitors');
        APP.example.visitors = (function () {

            var _visits_total = 0,  // private
                visitors      = []; // public

            // private function
            function _addVisit() {
                _visits_total += 1;
            }

            // public function
            function addVisitor(name) {
                visitors.push(name);
                _addVisit();
            }

            // public function
            function getVisitorsTotal() {
                return _visits_total;
            }

            // reveal public objects
            return {
                'visitors'   : visitors,
                'addVisitor' : addVisitor,
                'getVisitorsTotal' : getVisitorsTotal
            };

        }());


    - Using global object inside closure

        APP.namespace.create('example.usingJquery');
        APP.example.usingJquery = (function ($) {

            // you can use safely $ for jQuery
            $('#element').hide();

        }(jQuery));

        APP.namespace.create('example.usingMore');
        APP.example.usingMore = (function ($, _) {

            // you can use safely $ for jQuery lib
            $('#element').hide();

            // you can use safely _ for Lo-Dash (Underscore) lib
            var person = _.extend({ 'name': 'John' }, { 'age': 40 });

        }(jQuery, lodash));


    - More about module pattern

        http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth



Executable scripts:
-------------------

    compile.sh
        - Compile APP into packages and join plugins into one file.

    watch.sh
        - Watch js asset folder for a change and automatically compile.