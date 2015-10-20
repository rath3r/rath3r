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
