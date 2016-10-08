// include gulp
var gulp = require('gulp'); 

// include plug-ins
//var jshint = require('gulp-jshint');
var changed = require('gulp-changed');
var imagemin = require('gulp-imagemin');
var minifyHTML = require('gulp-minify-html');
var concat = require('gulp-concat');
//var stripDebug = require('gulp-strip-debug');
var uglify = require('gulp-uglify');
//var autoprefix = require('gulp-autoprefixer');
var cleanCSS = require('gulp-clean-css');
var minifyPHP = require('gulp-php-minify');

var concatDir = function(s){
    return "S:/servers/UniversityNetwork/v1.0/_dev/root/" + s;
}

//-- Tasks --------------------------------------------------

// Оптимизация JS
gulp.task('scripts', function() {
    gulp.src([concatDir('_src/js/app_config.js'), concatDir('_src/js/*.js'), concatDir('_src/js/**/*.js')])
        .pipe(concat('config.js'))
        //.pipe(uglify())
        .pipe(gulp.dest(concatDir('js/angularJS/')))
});

// Оптимизация HTML
gulp.task('htmlpage', function() {
    var sourceObjects = ['js/directives/**/*.html'];
    var dstDir = concatDir('ui.router/templates');
    
    for(i=0;i<sourceObjects.length;++i){
        srcDir = sourceObjects[i];
        gulp.src(srcDir)
            .pipe(changed(dstDir))
            .pipe(minifyHTML())
            .pipe(gulp.dest(dstDir));    
    }
});

// Оптимизация PHP
gulp.task('minify:php', function() {
    gulp.src(concatDir('_src/op/*.php'), {read: false})
        .pipe(minifyPHP({binary: 'S:/php7/php.exe'}))
        .pipe(gulp.dest(concatDir("op")));
});

// Сжатие изображений
gulp.task('imagemin', function() {
    var srcDir = concatDir('uploads/_temporary/images/*.*');
    var dstDir = concatDir('uploads/images');
    
    gulp.src(srcDir)
        .pipe(changed(dstDir))
        .pipe(imagemin())
        .pipe(gulp.dest(dstDir));
});

// Оптимизация CSS (после генерации SASS)
gulp.task('styles', function() {
    var srcDir = concatDir('_src/styles/*.css');
    var dstDir = concatDir('styles');
    
    gulp.src(srcDir)
        .pipe(concat('styles.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest(dstDir));
});

//--------------------------------------------------------------

// default gulp task
gulp.task('default', ['htmlpage', 'scripts', 'styles', 'minify:php'], function() {
    // watch for JS changes
    gulp.watch(concatDir('_src/js/angularJS/**/*'), function() {
        gulp.run('scripts');
    });
    
    gulp.watch(concatDir('_src/op/*.php'), function(){
        gulp.run('minify:php');
    })
    // TEMPORARY
    return;
    //-------------------
    
    // watch for HTML changes
    gulp.watch('S:/server/dev/src/*.html', function() {
        gulp.run('htmlpage');
    });

    

    // watch for CSS changes
    gulp.watch('S:/server/dev/src/styles/*.css', function() {
        gulp.run('styles');
    });
});