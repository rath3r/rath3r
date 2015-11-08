'use strict';

/**
 * @ngdoc overview
 * @name rath3rApp
 * @description
 * # rath3rApp
 *
 * Main module of the application.
 */
var rath3rApp = angular.module('rath3rApp', [
    'ngRoute',
    'ngSanitize'
]);

rath3rApp.config(function($routeProvider, $locationProvider) {
    $routeProvider

        // route for the home page
        .when('/', {
            templateUrl : 'views/main.html',
            controller  : 'mainCtrl'
        })

        .when('/post/:id', {
            templateUrl: 'views/post.html',
            controller: 'postCtrl'
        })
        // route for the about page
        .when('/about', {
            templateUrl : 'views/about.html',
            controller  : 'aboutCtrl'
        });

        // use the HTML5 History API
        $locationProvider.html5Mode(true);

        // route for the contact page
        //.when('/contact', {
        //    templateUrl : 'pages/contact.html',
        //    controller  : 'contactController'
        //});
});

//angular
//    .module('rath3rApp', [
//        'ngAnimate',
//        'ngCookies',
//        'ngResource',
//        'ngRoute',
//        'ngSanitize',
//        'ngTouch'
//    ])
//    .config(function ($routeProvider, $locationProvider) {
//        $routeProvider
//            .when('/', {
//                templateUrl: 'views/main.html',
//                controller: 'MainCtrl'
//            })
//            .when('/post/:id', {
//                templateUrl: 'views/post.html',
//                controller: 'PostCtrl'
//            })
//            .when('/about/', {
//                templateUrl: 'views/about.html',
//                controller: 'AboutCtrl'
//            })
//            .otherwise({
//                redirectTo: '/'
//            });
//
//        // use the HTML5 History API
//        $locationProvider.html5Mode(true);
//    });

/**
 * @ngdoc function
 * @name rath3rApp.controller:asboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the rath3rApp
 */
rath3rApp.controller('aboutCtrl', function($scope, $http) {

    $scope.message = 'This is the about page?';

    $scope.posts = {
        title: 'Loading'
    };

    $scope.loading = true;

    $http.get('http://dev.rath3rapi.com/json').
        success(function(data) {

            console.log(data);
            $scope.loading = false;

        }).
        error(function() {
        //data, status, headers, config
        // called asynchronously if an error occurs
        // or server returns response with an error status.
        });

});
/**
 * @ngdoc function
 * @name rath3rApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the rath3rApp
 */
rath3rApp.controller('mainCtrl', function($scope, $http) {

    // create a message to display in our view
    $scope.message = '';

    $scope.posts = {
        title: 'Loading'
    };

    $scope.loading = true;

    $scope.posts = {};

    $http.get('https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/').
        success(function(data) {

            $scope.posts = data.posts;

            $scope.loading = false;

        }).
        error(function() {
            //data, status, headers, config
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });

});
/**
 * @ngdoc function
 * @name rath3rApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the rath3rApp
 */
rath3rApp.controller('postCtrl', function($http, $scope, $routeParams) {

    $scope.post = {};

    $scope.loading = true;

    var postUrl = 'https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/' + $routeParams.id;

    $http.get(postUrl).
        success(function(data) {

            $scope.post = data;

            $scope.loading = false;

        }).
        error(function() {
            //data, status, headers, config
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
});
