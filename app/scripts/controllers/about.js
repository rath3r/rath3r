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

    $http.get('http://data.rath3r.com/json').
        success(function(data) {

            loadData(data);

            $scope.loading = false;

        }).
        error(function() {
        //data, status, headers, config
        // called asynchronously if an error occurs
        // or server returns response with an error status.
        });

    function loadData(skills) {

        var skillStartTime,
            skillStartTimeObj,
            skillStartYear,
            skillEndTime,
            totalTime,
            totalYear,
            width = 700,
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

        //totalYear =

        pxpertime = width / totalTime;

        for(var i = 0; i < skills.length; i++){

            translateY = Math.ceil((Date.parse(skills[i].dateStarted) - skillStartTime) * pxpertime);

            skillWidth = Math.ceil((currentTime - Date.parse(skills[i].dateStarted)) * pxpertime);

            data.push(
                {
                    y       : translateY,
                    title   : skills[i].title,
                    width   : skillWidth
                }
            );
        }

        chart = d3.select(".chart")
            .attr("width", width)
            .attr("height", ((barHeight * data.length) + 100));

        bar = chart.selectAll("g")
            .data(data)
            .enter().append("g")
            .attr("transform", function(d, i) { return "translate(" + d.y + "," + ((i * barHeight) + 20) + ")"; });

        bar.append("rect")
            .attr("width", function(d, i) { return d.width; })
            .attr("height", barHeight - 1);

        bar.append("text")
            .attr("x", function(d) { return 10; })
            .attr("y", barHeight / 2)
            .attr("dy", ".35em")
            .text(function(d) { return d.title; });

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
});