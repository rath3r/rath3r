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

  $scope.skillsLoader = true;
  $scope.sitesLoader = true;

  // $http.get('http://data.rath3r.com/json').success(function(data) {
  $http.get('http://dev.rath3rapi.com/json/skills').success(function(data) {
    loadSkills(data);
    $scope.skillsLoader = false;
  }).error(function() {
    //data, status, headers, config
    // called asynchronously if an error occurs
    // or server returns response with an error status.
  });

  $http.get('http://dev.rath3rapi.com/json/sites').success(function(data) {
    loadSites(data);
    $scope.sitesLoader = false;
  }).error(function() {});

  function loadSkills(skills) {

    var skillStartTime,
        skillStartTimeObj,
        skillStartYear,
        skillEndTime,
        totalTime,
        totalYear,
        width = $("#chartHolder").width(),
        pxpertime,
        currentTimeObj = new Date(),
        currentTime = currentTimeObj.getTime(),
        currentYear = currentTimeObj.getFullYear(),
        data = [],
        translateY,
        skillWidth,
        barHeight = 20,
        chart,
        bar;

    function compare(a,b) {
      if (a.dateStarted < b.dateStarted) {
        return -1;
      }
      if (a.dateStarted > b.dateStarted) {
          return 1;
      }
      return 0;
    }

    skills.sort(compare);

    skillStartTime = Math.min.apply(null,
      Object.keys(skills).map(function(e) {
        return Date.parse(skills[e].dateStarted);
      })
    );

    skillEndTime = Math.max.apply(null,
      Object.keys(skills).map(function(e) {
        return Date.parse(skills[e].dateStarted);
      })
    );

    totalTime = currentTime - skillStartTime;

    skillStartTimeObj = new Date(skillStartTime);
    skillStartYear = skillStartTimeObj.getFullYear();

    pxpertime = width / totalTime;

    for(var i = 0; i < skills.length; i++){

      translateY = Math.ceil(
        (Date.parse(skills[i].dateStarted) - skillStartTime) * pxpertime
      );

      if(skills[i].dateFinished && !skills[i].stillUsing) {
        skillWidth = Math.ceil(
          (Date.parse(skills[i].dateFinished) - Date.parse(skills[i].dateStarted)) * pxpertime
        );
      } else {
        skillWidth = Math.ceil(
          (currentTime - Date.parse(skills[i].dateStarted)) * pxpertime
        );
      }

      data.push({
        y       : translateY,
        title   : skills[i].title,
        width   : skillWidth
      });
    }

    chart = d3.select(".chart")
      .attr("width", width)
      .attr("height", ((barHeight * data.length)));

    bar = chart.selectAll("g")
      .data(data)
      .enter().append("g")
      .attr("transform", function(d, i) {
        return "translate(" + d.y + "," + ((i * barHeight) + 20) + ")";
      });

    bar.append("rect")
      .attr("width", function(d, i) {
        return d.width;
      })
      .attr("height", barHeight - 1);

    bar.append("text")
      .attr("x", function(d) { return 10; })
      .attr("y", barHeight / 2)
      .attr("dy", ".35em")
      .text(function(d) {
        return d.title;
      });

    d3.select(".chart")
      .append("text")
      .attr("class", "label")
      .attr("x", 0)
      .attr("y", 10)
      .attr("dy", ".35em")
      .text(skillStartYear);

    d3.select(".chart")
      .append("text")
      .attr("class", "label")
      .attr("x", (width - 25))
      .attr("y", 10)
      .attr("dy", ".35em")
      .text(currentYear);

  }

  function loadSites(sites) {

    $scope.sites = sites;

    for(var i = 0; i < sites.length; i++){
        console.log(sites[i]);

        for(var j = 0; j < sites[i].skills.length;j++){
            console.log(sites[i].skills[j]);
        }
    }
  }
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

  var wordpressUrl = 'https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/';

  $http.get(wordpressUrl).success(function(data) {
    
    //console.log(data);
    $scope.posts = data.posts;
    $scope.loading = false;

  }).error(function() {
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
