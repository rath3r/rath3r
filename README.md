# Rath3r

This is the frontend main page of [rath3r.com][1]. The content is being drawn
from a [wordpress][2] blog and from an API hosted on [data.rath3r.com][3].

## Version

This is version 2.0 of the [rath3r][1] site. It represents a move from Vanilla
to Angular.

This project is generated with [yo angular generator][4] version 0.11.1.

## Initial Setup

The `#/` was removed from the url by following these instructions -
[pretty-urls-in-angularjs-removing-the-hashtag][5]

Add to the `.htaccess` also stored in `app/`

    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_URI} !^(/index\.php|/img|/js|/css|/robots\.txt|/favicon\.ico)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.html [L]

## Build & development

Run `grunt` for building and `grunt serve` for preview.

## Notes

The loading gifs are coming from here [preloaders][6]

[1]: http://rath3r.com
[2]: https://en-gb.wordpress.org/
[3]: http://data.rath3r.com
[4]: https://github.com/yeoman/generator-angular
[5]: https://scotch.io/quick-tips/pretty-urls-in-angularjs-removing-the-hashtag
[6]: http://preloaders.net/
