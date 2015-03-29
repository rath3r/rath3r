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
            title: "Loading"
        };

        $scope.loading = true;

        $http.get('https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/').
            success(function(data, status, headers, config) {
                console.log(status);
                //console.log(headers);
                //console.log(config);
                console.log(data.posts);

                $scope.posts = {};

                //for(post in data[posts]){
                //    console.log(post);
                //}
                $scope.posts = data.posts;

                $scope.loading = false;

            }).
            error(function(data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });

        console.log('hello');

    });
