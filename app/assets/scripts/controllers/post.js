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
