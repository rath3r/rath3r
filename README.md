#Rath3r

This is the frontend main page of rath3r.com.

A quick setup using [Gulp][1] and flat file sites. A tutorial on [tutsplus][2]
was used for some pointers. [addyosmani.com/largescalejavascript][11] inspired
the modules.

Usage:

* `npm install` to setup.
* `npm test` to test.

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

Currently using [concat][7] to pull all the javascript together.

### Bootstrap

For quick prototyping [Bootstrap][9] has been added via [Yarn][10].

[1]:http://gulpjs.com/
[2]:http://code.tutsplus.com/tutorials/gulp-as-a-development-web-server--cms-20903
[3]:https://www.npmjs.com/package/gulp-connect
[4]:https://www.npmjs.com/package/gulp-less
[5]:https://www.npmjs.com/package/gulp-sass
[6]:https://www.npmjs.com/package/gulp-twig
[7]:https://github.com/contra/gulp-concat
[8]:https://www.npmjs.com/package/gulp-clean
[9]:http://getbootstrap.com/
[10]:https://yarnpkg.com/
[11]:https://addyosmani.com/largescalejavascript/
