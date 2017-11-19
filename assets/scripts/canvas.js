// https://addyosmani.com/largescalejavascript/

var Canvas = (function(){
    var canvas,
        ctx,
        req,
        width,
        height,
        init = function() {
            canvas = document.getElementById('canvas');
            if (canvas.getContext) {
                ctx = canvas.getContext('2d');
            }
            setupPage();
            req = window.requestAnimationFrame(drawTrain);
        },
        setupPage = function () {
            width = $('#canvasHolder').width();
            $('#canvas').attr('width', width);
            height = $('#canvas').height();
            $('#canvas').attr('height', height);
            $('#canvas').width(width);
            ctx.globalCompositeOperation = 'destination-over';
            addControls();
        },
        canvasTest = function (){
            ctx.fillStyle = 'red';
            ctx.fillRect(10, 10, 100, 100);
        },
        drawArcs = function (){
            ctx = canvas.getContext('2d');

            for (var i = 0; i < 4; i++) {
                for (var j = 0; j < 3; j++) {
                    ctx.beginPath();
                    var x = 25 + j * 50; // x coordinate
                    var y = 25 + i * 50; // y coordinate
                    var radius = 20; // Arc radius
                    var startAngle = 0; // Starting point on circle
                    var endAngle = Math.PI + (Math.PI * j) / 2; // End point on circle
                    var anticlockwise = i % 2 !== 0; // clockwise or anticlockwise

                    ctx.arc(x, y, radius, startAngle, endAngle, anticlockwise);

                    if (i > 1) {
                        ctx.fill();
                    } else {
                        ctx.stroke();
                    }
                }
            }
        },
        drawText = function() {
            ctx.font = '48px serif';
            ctx.fillText('Hello world', 10, 50)
        },
        drawNumber = function(number, x = 25, y = 10) {
            ctx.save();
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = 'black';
            ctx.font = '20px Times New Roman';
            ctx.fillText(number, x, y);
            ctx.restore();
            ctx.restore();
        },
        startX = 100,
        startY = 100,
        endX = 800,
        endY = 100,
        posX, posY,
        dx = 0;
        drawTrain = function() {
            var now = new Date();
            var sec = now.getSeconds();
            var milli = now.getMilliseconds();
            var len = 100;

            ctx.save();
            ctx.clearRect(0, 0, width, height);

            ctx.beginPath();
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 1;
            ctx.moveTo(startX, startY);
            ctx.lineTo(endX, endY);
            ctx.stroke();

            ctx.save();
            if (!posX) {
                posX = startX//window.requestAnimationFrame(drawTrain);;
            } else {
                if (posX < (endX - len)) {
                    posX = posX + dx;

                    drawEnd('green');
                } else {
                    drawEnd('red');
                    window.cancelAnimationFrame(req);
                }
            }

            if (!posY) {
                posY = 75;
            }
            // if (posX > endx) {
            //     posX = endx;
            // }
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 1;
            //             x    y   len  hei
            ctx.strokeRect(posX, posY, len, 50);
            ctx.clearRect((posX + 1), (posY + 1), len - 2, 50)

            ctx.restore();

            req = window.requestAnimationFrame(drawTrain);
        },
        addControls = function() {
            var speedEl = document.getElementById('speed');
            var speedValEl = document.getElementById('speedVal');
            var resetEl = document.getElementById('reset');

            speedEl.addEventListener("input", function() {
                dx = speedEl.value / 8;
                speedValEl.value = dx;
            }, false);

            resetEl.addEventListener("click", function() {
                posX = startX;
            }, false);
        },
        drawEnd = function(color) {
            ctx.beginPath();
            ctx.moveTo(endX, endY - 35);
            ctx.lineTo(endX, endY + 35);
            ctx.strokeStyle = color;
            ctx.lineWidth = 1;
            ctx.stroke();
        };
    return {
    init: init
}
}())

App.init();


// clock = function() {
//     var now = new Date();
//     ctx.save();
//     ctx.clearRect(0, 0, 150, 150);
//     ctx.translate(75, 75);
//     ctx.scale(0.4, 0.4);
//     ctx.rotate(-Math.PI / 2);
//     ctx.strokeStyle = 'black';
//     ctx.fillStyle = 'white';
//     ctx.lineWidth = 8;
//     ctx.lineCap = 'round';
//
//     // Hour marks
//     // ctx.save();
//     // for (var i = 0; i < 12; i++) {
//     //     ctx.beginPath();
//     //     ctx.rotate(Math.PI / 6);
//     //     ctx.moveTo(100, 0);
//     //     ctx.lineTo(120, 0);
//     //     ctx.stroke();
//     // }
//     // ctx.restore();
//
//     // // Minute marks
//     // ctx.save();
//     // ctx.lineWidth = 5;
//     // for (i = 0; i < 60; i++) {
//     //     if (i % 5!= 0) {
//     //         ctx.beginPath();
//     //         ctx.moveTo(117, 0);
//     //         ctx.lineTo(120, 0);
//     //         ctx.stroke();
//     //     }
//     //     ctx.rotate(Math.PI / 30);
//     // }
//     // ctx.restore();
//
//     var sec = now.getSeconds();
//     var min = now.getMinutes();
//     var hr  = now.getHours();
//     hr = hr >= 12 ? hr - 12 : hr;
//
//     // ctx.fillStyle = 'black';
//
//     // write Hours
//     // ctx.save();
//     // ctx.rotate(hr * (Math.PI / 6) + (Math.PI / 360) * min + (Math.PI / 21600) *sec);
//     // ctx.lineWidth = 14;
//     // ctx.beginPath();
//     // ctx.moveTo(-20, 0);
//     // ctx.lineTo(80, 0);
//     // ctx.stroke();
//     // ctx.restore();
//
//     // write Minutes
//     // ctx.save();
//     // ctx.rotate((Math.PI / 30) * min + (Math.PI / 1800) * sec);
//     // ctx.lineWidth = 10;
//     // ctx.beginPath();
//     // ctx.moveTo(-28, 0);
//     // ctx.lineTo(112, 0);
//     // ctx.stroke();
//     // ctx.restore();
//
//     // Write seconds
//     ctx.save();
//     ctx.rotate(sec * Math.PI / 30);
//     ctx.strokeStyle = '#D40000';
//     ctx.fillStyle = '#D40000';
//     ctx.lineWidth = 6;
//     ctx.beginPath();
//     ctx.moveTo(-30, 0);
//     ctx.lineTo(83, 0);
//     ctx.stroke();
//     ctx.beginPath();
//     ctx.arc(0, 0, 10, 0, Math.PI * 2, true);
//     ctx.fill();
//     ctx.beginPath();
//     ctx.arc(95, 0, 10, 0, Math.PI * 2, true);
//     ctx.stroke();
//     ctx.fillStyle = 'rgba(0, 0, 0, 0)';
//     ctx.arc(0, 0, 3, 0, Math.PI * 2, true);
//     ctx.fill();
//     ctx.restore();
//
//     // ctx.beginPath();
//     // ctx.lineWidth = 14;
//     // ctx.strokeStyle = '#325FA2';
//     // ctx.arc(0, 0, 142, 0, Math.PI * 2, true);
//     // ctx.stroke();
//     //
//     ctx.restore();
//
//     window.requestAnimationFrame(clock);
// }
