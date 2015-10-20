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
