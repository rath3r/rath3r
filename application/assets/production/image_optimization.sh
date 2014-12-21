#!/bin/bash

PROGPATH=$(dirname $_)

echo ""
echo "--------------------"
echo " IMAGE OPTIMIZATION "
echo "--------------------"
echo ""

echo "Optimizing PNG images in '/wwwroot/images':"

cd $PROGPATH
cd ../../wwwroot/images

LIST=$(find . -type f -name "*.png" -print)
oneLineFile=

for path in $LIST
do
    oneLineFile="$oneLineFile ${path:2}"
done

eval "java -cp $PROGPATH/pngtastic.jar com.googlecode.pngtastic.Pngtastic $oneLineFile"

echo ""