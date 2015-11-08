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