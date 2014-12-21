
================================================================================
 PRODUCTION DEPLOYMENT SCRIPTS
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

    For minification - Java Enviroment
    (YUI Compressor)



Installation:
-------------

    Ryby Gems - see ../css/_README.txt



Executable scripts:
-------------------

    local_test.sh
        - Use this for testing before deploment to server to check
          if there are no issues after minification
        - Operations executed:
            * Force css and js assets to recompile
            * Minification of assets

    server.sh
        - Use this to execute on server after deployment or as a part
          of the deployment script
        - Assets should NOT be committed already minified because it will
          be minified twice. It should work but it might case issues or
          slightly increase file size
        - Operations executed:
            * Minification of css and js assets

    image_optimization.sh
        - Experimental feature
        - It is working fine but the Java app used for optimization is
          quite new = possible bugs
        - Operations executed:
            * Optimize just PNG images for now