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