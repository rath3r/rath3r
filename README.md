# rath3r

[rath3r.com](http://rath3r.com)

This is the frontend main page of rath3r.com. The content is being drawn from a [wordpress](https://en-gb.wordpress.org/) 
blog and from an API hosted on [data.rath3r.com](http://data.rath3r.com).

## Version

This is version 2.0 of the rath3r site. It represents a move from Vanilla to Angular.

This project is generated with [yo angular generator](https://github.com/yeoman/generator-angular) version 0.11.1.

## Initial Setup

I removed the use of the `#/` in the url by following this instructions - 
[pretty-urls-in-angularjs-removing-the-hashtag](https://scotch.io/quick-tips/pretty-urls-in-angularjs-removing-the-hashtag)
 
Add to the `.htaccess` also stored in `app/`

    RewriteEngine On
    RewriteBase /
    RewriteCond     %{REQUEST_URI} !^(/index\.php|/img|/js|/css|/robots\.txt|/favicon\.ico)
    RewriteCond     %{REQUEST_FILENAME} !-f
    RewriteCond     %{REQUEST_FILENAME} !-d
    RewriteRule     .               /index.html              [L]

## Build & development

Run `grunt` for building and `grunt serve` for preview.

## Testing

Running `grunt test` will run the unit tests with karma.

## Notes

The loading gifs are coming from here [preloaders](http://preloaders.net/)
