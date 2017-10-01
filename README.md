#Rath3r

This is the frontend main page of rath3r.com.

A quick setup using [Gulp][1] and flat file sites. A tutorial on [tutsplus][2] was used for some pointers.

`npm init` was used to create the `package.json` file.

Use `npm install` to setup.

`bower init` was used to create the `bower.json`. Install the sites css and js
dependancies using `bower install`. Don't forget the `--save` option on `bower`
install to save new dependancies in the `bower.json`.

## Features

### Connect

A server is created using [gulp-connect][3] which has watch and livereload
configured.

### Clean

Gotta [clean][8] the dist before building.

### Less

[Less][4] for the styles although [Sass][5] could be installed just as quick.

    npm install gulp-sass --save-dev

### Twig

Rather than just using `.html` files [twig][6] is being used for templates.

### Concat

Currently using [concat][7] to pull all the javascript together. Maybe something
like coffee could be the way forward.

### Bootstrap

For quick prototyping [Bootstrap][9] has been added via `bower`.

## Troubleshooting

### Unexplained error on startup

Occasionally if the server has not been shutdown correctly you will get an error
the next time you run `gulp`. To fix this list all the processes running on the
target port and `kill` the `node` processes.

For example:

    $ lsof -i tcp:8000
    COMMAND   PID    USER   FD   TYPE             DEVICE SIZE/OFF NODE NAME
    node    16552  rath3r   11u  IPv4 0xffffff8022458da8      0t0  TCP *:hbci (LISTEN)
    kill -9 16552

[1]:http://gulpjs.com/
[2]:http://code.tutsplus.com/tutorials/gulp-as-a-development-web-server--cms-20903
[3]:https://www.npmjs.com/package/gulp-connect
[4]:https://www.npmjs.com/package/gulp-less
[5]:https://www.npmjs.com/package/gulp-sass
[6]:https://www.npmjs.com/package/gulp-twig
[7]:https://github.com/contra/gulp-concat
[8]:https://www.npmjs.com/package/gulp-clean
[9]:http://getbootstrap.com/
