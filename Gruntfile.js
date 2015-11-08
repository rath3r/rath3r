// Generated on 2015-03-29 using generator-angular 0.11.1
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    // Configurable paths for the application
    var appConfig = {
        app:    require('./bower.json').appPath || 'app',
        dist:   'dist',
        tmp:    '.tmp'
    };

    // Define the configuration for all the tasks
    grunt.initConfig({

        // Project settings
        settings: appConfig,

        // Watches files for changes and runs tasks based on the changed files
        watch: {
            bower: {
                files: ['bower.json'],
                tasks: ['wiredep']
            },
            scripts: {
                files: ['<%= settings.app %>/scripts/{,*/}*.js'],
                tasks: ['concat:scripts'],
            },
            js: {
                files: ['<%= settings.dist %>/scripts/{,*/}*.js'],
                tasks: ['newer:jshint:dist'],
            },
            styles: {
                files: ['<%= settings.app %>/styles/{,*/}*.less'],
                tasks: ['less', 'autoprefixer:dist']
            },
            cssmin: {
                files: ['<%= settings.tmp %>/styles/{,*/}*.css'],
                tasks: ['cssmin']
            },
            index: {
                files: [
                    '<%= settings.app %>index.html'
                ],
                tasks: ['copy:index']
            },
            views: {
                files: [
                    '<%= settings.app %>/views/{,*/}*.html'
                ],
                tasks: ['newer:copy:views']
            },
            usemin: {
                files: ['<%= settings.app %>{,*/}*.html'],
                tasks: ['usemin']
            }
        },

        // Make sure code styles are up to par and there are no obvious mistakes
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                reporter: require('jshint-stylish')
            },
            all: {
                src: [
                    'Gruntfile.js',
                    '<%= settings.app %>/scripts/{,*/}*.js'
                ]
            },
            dist: {
                src: [
                    '<%= settings.dist %>/scripts/main.js'
                ]
            },
            test: {
                options: {
                    jshintrc: 'test/.jshintrc'
                },
                src: ['test/spec/{,*/}*.js']
            }
        },

        // Empties folders to start fresh
        clean: {
            dist: {
                files: [{
                    dot: true,
                    src: [
                        '.tmp',
                        '<%= settings.dist %>/{,*/}*',
                        '!<%= settings.dist %>/.git{,*/}*'
                    ]
                }]
            },
            server: '.tmp'
        },

        // Add vendor prefixed styles
        autoprefixer: {
            options: {
                browsers: ['last 1 version']
            },
            server: {
                options: {
                    map: true,
                },
                files: [{
                    expand: true,
                    cwd: '.tmp/styles/',
                    src: '{,*/}*.css',
                    dest: '.tmp/styles/'
                }]
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '.tmp/styles/',
                    src: '{,*/}*.css',
                    dest: '.tmp/styles/'
                }]
            }
        },

        // Automatically inject Bower components into the app
        wiredep: {
            app: {
                src: ['<%= settings.app %>/index.html'],
                ignorePath:  /\.\.\//
            },
            test: {
                devDependencies: true,
                src: '<%= karma.unit.configFile %>',
                ignorePath:  /\.\.\//,
                fileTypes:{
                    js: {
                        block: /(([\s\t]*)\/{2}\s*?bower:\s*?(\S*))(\n|\r|.)*?(\/{2}\s*endbower)/gi,
                        detect: {
                            js: /'(.*\.js)'/gi
                        },
                        replace: {
                            js: '\'{{filePath}}\','
                        }
                    }
                }
            }
        },

        // Renames files for browser caching purposes
        filerev: {
            dist: {
            src: [
                '<%= settings.dist %>/scripts/{,*/}*.js',
                '<%= settings.dist %>/styles/{,*/}*.css',
                '<%= settings.dist %>/images/{,*/}*.{png,jpg,jpeg,gif,webp,svg}',
                '<%= settings.dist %>/styles/fonts/*'
            ]}
        },

        // Reads HTML for usemin blocks to enable smart builds that automatically
        // concat, minify and revision files. Creates configurations in memory so
        // additional tasks can operate on them
        useminPrepare: {
            html: '<%= settings.app %>/index.html',
            options: {
                dest: '<%= settings.dist %>',
                flow: {
                    html: {
                        steps: {
                            js: [
                                'concat',
                                'uglifyjs'
                            ],
                            css: ['cssmin']
                        },
                        post: {}
                    }
                }
            }
        },

        // Performs rewrites based on filerev and the useminPrepare configuration
        usemin: {
            html: ['<%= settings.dist %>/{,*/}*.html'],
            css: ['<%= settings.dist %>/styles/{,*/}*.css'],
            options: {
                assetsDirs: [
                    '<%= settings.dist %>',
                    '<%= settings.dist %>/images',
                    '<%= settings.dist %>/styles'
                ]
            }
        },


        // The following *-min tasks will produce minified files in the dist folder
        // By default, your `index.html`'s <!-- Usemin block --> will take care of
        // minification. These next options are pre-configured if you do not wish
        // to use the Usemin blocks.
        cssmin: {
            dist: {
                files: {
                    '<%= settings.dist %>/styles/main.css': [
                        '<%= settings.tmp %>/styles/{,*/}*.css'
                    ]
                }
            }
        },
        // uglify: {
        //   dist: {
        //     files: {
        //       '<%= settings.dist %>/scripts/scripts.js': [
        //         '<%= settings.dist %>/scripts/scripts.js'
        //       ]
        //     }
        //   }
        // },
         concat: {
            scripts: {
                files: {
                    '<%= settings.dist %>/scripts/main.js': [
                        '<%= settings.app %>/scripts/{,*/}*.js'
                    ]
                }
            }
         },

        imagemin: {
            dist: {
                files: [{
                    expand: true,
                    cwd: '<%= settings.app %>/images',
                    src: '{,*/}*.{png,jpg,jpeg,gif}',
                    dest: '<%= settings.dist %>/images'
                }]
            }
        },

        svgmin: {
            dist: {
                files: [{
                    expand: true,
                    cwd: '<%= settings.app %>/images',
                    src: '{,*/}*.svg',
                    dest: '<%= settings.dist %>/images'
                }]
            }
        },

        htmlmin: {
            dist: {
                options: {
                    collapseWhitespace: true,
                    conservativeCollapse: true,
                    collapseBooleanAttributes: true,
                    removeCommentsFromCDATA: true,
                    removeOptionalTags: true
                },
                files: [{
                    expand: true,
                    cwd: '<%= settings.dist %>',
                    src: ['*.html', 'views/{,*/}*.html'],
                    dest: '<%= settings.dist %>'
                }]
            }
        },

        // ng-annotate tries to make the code safe for minification automatically
        // by using the Angular long form for dependency injection.
        ngAnnotate: {
            dist: {
                files: [{
                    expand: true,
                    cwd: '.tmp/concat/scripts',
                    src: '*.js',
                    dest: '.tmp/concat/scripts'
                }]
            }
        },

        // Replace Google CDN references
        cdnify: {
            dist: {
                html: ['<%= settings.dist %>/*.html']
            }
        },

        // Copies remaining files to places other tasks can use
        copy: {
            dist: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: '<%= settings.app %>',
                    dest: '<%= settings.dist %>',
                    src: [
                        '*.{ico,png,txt}',
                        '.htaccess',
                        'images/{,*/}*.{webp}',
                        'styles/fonts/{,*/}*.*'
                    ]
                }, {
                    expand: true,
                    cwd: '.tmp/images',
                    dest: '<%= settings.dist %>/images',
                    src: ['generated/*']
                }, {
                    expand: true,
                    cwd: 'bower_components/bootstrap/dist',
                    src: 'fonts/*',
                    dest: '<%= settings.dist %>'
                }]
            },
            styles: {
                expand: true,
                cwd: '<%= settings.app %>/styles',
                dest: '.tmp/styles/',
                src: '{,*/}*.css'
            },
            index: {
                expand: true,
                cwd: '<%= settings.app %>',
                dest: '<%= settings.dist %>',
                src: [
                    'index.html',
                ]
            },
            views: {
                expand: true,
                cwd: '<%= settings.app %>',
                dest: '<%= settings.dist %>',
                src: [
                    'views/{,*/}*.html',
                ]
            }
        },

        // Run some tasks in parallel to speed up the build process
        concurrent: {
            server: [
                'copy:styles'
            ],
            test: [
                'copy:styles'
            ],
            dist: [
                'copy:styles',
                'imagemin',
                'svgmin'
            ]
        },

        less: {
            dist: {
                files: {
                    "<%= settings.tmp %>/styles/main.css": "<%= settings.app %>/styles/less/main.less"
                }
            },
        },

        // Test settings
        karma: {
            unit: {
                configFile: 'test/karma.conf.js',
                singleRun: true
            }
        }
    });


    grunt.registerTask('serve', 'Compile then start a connectconnect web server', function (target) {
        if (target === 'dist') {
            return grunt.task.run(['build', 'connect:dist:keepalive']);
        }

        grunt.task.run([
            'clean:server',
            'wiredep',
            'concurrent:server',
            'autoprefixer:server',
            'connect:livereload',
            'watch'
        ]);
    });

    grunt.registerTask('server', 'DEPRECATED TASK. Use the "serve" task instead', function (target) {
        grunt.log.warn('The `server` task has been deprecated. Use `grunt serve` to start a server.');
        grunt.task.run(['serve:' + target]);
    });

    grunt.registerTask('test', [
        'clean:server',
        'wiredep',
        'concurrent:test',
        'autoprefixer',
        'connect:test',
        'karma'
    ]);

    //'cdnify',
    grunt.registerTask('build', [
        'clean:dist',
        'wiredep',
        'useminPrepare',
        'concurrent:dist',
        'autoprefixer',
        'concat',
        'ngAnnotate',
        'copy:dist',
        'copy:index',
        'copy:views',
        'less',
        'cssmin',
        'uglify',
        //'filerev',
        'usemin',
        //'htmlmin'
    ]);

    grunt.registerTask('default', [
        //'newer:jshint',
        //'test',
        'build'
    ]);
};
