# rath3r

## Version

This is version 2.0 of the rath3r site. It represents a move from Vanilla to Angular.

This project is generated with [yo angular generator](https://github.com/yeoman/generator-angular)
version 0.11.1.

## Initial Setup

I removed the use of the `#/` in the url by following this instructions - [pretty-urls-in-angularjs-removing-the-hashtag](https://scotch.io/quick-tips/pretty-urls-in-angularjs-removing-the-hashtag)
 
Add to the `.htaccess`

    RewriteCond %{REQUEST_FILENAME} -s [OR]
    RewriteCond %{REQUEST_FILENAME} -l [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^.*$ - [NC,L]
    
    RewriteRule ^(.*) /index.html [NC,L]

## Build & development

Run `grunt` for building and `grunt serve` for preview.

## Testing

Running `grunt test` will run the unit tests with karma.
