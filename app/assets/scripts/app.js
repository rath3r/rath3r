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

});
