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

        var skillsdatelast = Math.max.apply(null,
                Object.keys(skills).map(function(e) {
                    return Date.parse(skills[e].dateStarted);
                })
            ),
            skillsdatefirst = Math.min.apply(null,
                Object.keys(skills).map(function(e) {
                    return Date.parse(skills[e].dateStarted);
                })
            ),
            currentDateObj = new Date(),
            currentDate = currentDateObj.getTime(),
            currentYear,
            data = [],
            diff,
            translateY,
            currentDiff,
            width = 700,
            barHeight = 20,
            chart,
            bar,
            myDate,
            currentWidth,
            translateWidthY,
            skillsdatefirstObj,
            skillStartYear;

        diff = currentDate - skillsdatefirst;

        skillsdatefirstObj = new Date(skillsdatefirst);
        skillStartYear = skillsdatefirstObj.getFullYear();
        currentYear = currentDateObj.getFullYear();

        for(var i = 0; i < skills.length; i++){

            currentDiff = currentDate - Date.parse(skills[i].dateStarted);

            if (diff == currentDiff) {
                translateY = 0;
            } else {
                translateY = (currentDiff * width) / diff;
            }


            if (skills[i].stillUsing == 1) {
                translateWidthY = (currentDate * width) / diff;
            } else {
                currentWidth = Date.parse(skills[i].dateFinished);
                translateWidthY = ((currentDate - currentWidth) * width) / diff;
            }

            data.push(
                {
                    y       : translateY,
                    title   : skills[i].title,
                    width   : translateWidthY
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