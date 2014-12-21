
================================================================================
 CSS ASSET - SASS and COMPASS
================================================================================


System requirements:
--------------------

    Ruby Gem version 1.8.24
    (get Ruby Gem version: gem -v)

    Ruby Gems:
    (get Ruby Gem list: gem list)
        - sass (3.2.1)
        - compass (0.12.2)
        - html5-boilerplate (2.1.0)
        - compass-h5bp (0.0.5)



Installation:
-------------

    1. Update Ryby Gem:

        sudo gem update --system

    2. Install SASS and Compass:

        sudo gem install sass
        sudo gem install compass

    3. Install Compass extensions:

        sudo gem install html5-boilerplate
        sudo gem install compass-h5bp

    4. Install faster image/sprite generator:

        sudo gem install oily_png


Updating Gems:
--------------

    sudo gem update <gem_name>



Project settings:
-----------------

    SASS and Compass settings are in 'config.rb' file.



Executable scripts:
-------------------

    recompile.sh
        - Force recompile sass files (no cache is used).

    watch.sh
        - Watch css asset folder for a change and automatically recompile.