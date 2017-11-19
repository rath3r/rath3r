'use strict';

var gulp = require('gulp'),
  connect = require('gulp-connect'),
  sass = require('gulp-sass'),
  twig = require('gulp-twig'),
  concat = require('gulp-concat'),
  clean = require('gulp-clean'),
  fs = require('fs'),
  packagejson = JSON.parse(fs.readFileSync('./package.json'));;


gulp.task('webserver', function() {
  connect.server({
      livereload: true,
      root: ['dist']
  });
});

gulp.task('livereload', function() {
  gulp.src([
      'assest/styles/*.scss',
      'views/*.twig'
    ]).pipe(watch())
    .pipe(connect.reload());
});

gulp.task('clean', function () {
    gulp.src('dist/*', {read: false})
        .pipe(clean({force: true}));
});

gulp.task('twig', function () {
    return gulp.src([
      'views/*.twig',
      'views/pages/*.twig'
    ])
        .pipe(twig({
            data: {
                title: 'rath3r',
                author: packagejson.author,
                description: 'The rath3r site',
                benefits: [
                    'Fast',
                    'Flexible',
                    'Secure'
                ]
            }
        }))
        .pipe(gulp.dest('dist/'))
        .pipe(connect.reload());
});

gulp.task('sass', function() {
  gulp.src('assets/styles/main.scss')
    .pipe(sass())
    .pipe(gulp.dest('dist/styles'))
    .pipe(connect.reload());
});

gulp.task('scripts', function() {
  return gulp.src('assets/scripts/**/*.js')
    .pipe(concat('main.js'))
    .pipe(gulp.dest('dist/scripts/'))
    .pipe(connect.reload());
});

gulp.task('bootstrap', function() {

  return gulp.src('./node_modules/bootstrap/dist/js/bootstrap.min.js')
    .pipe(gulp.dest('./dist/scripts'));
});

gulp.task('watch', function() {
    gulp.watch('assets/styles/*.scss', ['less']);
    gulp.watch('views/**/*.twig', ['twig']);
    gulp.watch('assets/scripts/**/*.js', ['scripts']);
});

gulp.task('default', [
  'twig',
  'sass',
  'scripts',
  'bootstrap',
  'webserver',
  'watch'
]);
