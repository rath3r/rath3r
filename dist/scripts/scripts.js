'use strict';

/**
 * @ngdoc overview
 * @name rath3rApp
 * @description
 * # rath3rApp
 *
 * Main module of the application.
 */
angular
    .module('rath3rApp', [
        'ngAnimate',
        'ngCookies',
        'ngResource',
        'ngRoute',
        'ngSanitize',
        'ngTouch'
    ])
    .config(function ($routeProvider, $locationProvider) {
        $routeProvider
            .when('/', {
                templateUrl: 'views/main.html',
                controller: 'MainCtrl'
            })
            .when('/about', {
                templateUrl: 'views/about.html',
                controller: 'AboutCtrl'
            })
            .when('/post/:id', {
                templateUrl: 'views/post.html',
                controller: 'PostCtrl'
            })
            .otherwise({
                redirectTo: '/'
            });

        // use the HTML5 History API
        $locationProvider.html5Mode(true);
    });

'use strict';

/**
 * @ngdoc function
 * @name rath3rApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the rath3rApp
 */
angular.module('rath3rApp')
    .controller('MainCtrl', function ($scope, $http) {

        $scope.awesomeThings = [
            'HTML5 Boilerplate',
            'AngularJS',
            'Karma'
        ];

        $scope.posts = {
            title: 'Loading'
        };

        $scope.loading = true;

        $http.get('https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/').
            success(function(data) {

                // , status, headers, config
                //console.log(status);
                //console.log(headers);
                //console.log(config);
                //console.log(data.posts);

                $scope.posts = {};

                //for(post in data[posts]){
                //    console.log(post);
                //}

                $scope.posts = data.posts;

                $scope.loading = false;

            }).
            error(function() {
                //data, status, headers, config
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });

    });

'use strict';

/**
 * @ngdoc function
 * @name rath3rApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the rath3rApp
 */
angular.module('rath3rApp')
  .controller('AboutCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });

'use strict';

/**
 * @ngdoc function
 * @name rath3rApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the rath3rApp
 */
angular.module('rath3rApp')
    .controller('PostCtrl', function ($http, $scope, $routeParams) {

        $scope.awesomeThings = [
            'HTML5 Boilerplate',
            'AngularJS',
            'Karma'
        ];

        $scope.post = {};

        $scope.loading = true;

        var postUrl = 'https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/' + $routeParams.id;

        $http.get(postUrl).
            success(function(data) {
                // , status, headers, config
                //console.log(data);
                //console.log(headers);
                //console.log(config);
                //console.log(data.posts);

                //$scope.post = {};

                //for(post in data[posts]){
                //    console.log(post);
                //}

                $scope.post = data;

                $scope.loading = false;

            }).
            error(function() {
                //data, status, headers, config
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    });
