#!/bin/bash

PROGPATH=$(dirname $_)

clear

echo ""
echo "=========================="
echo " ASSETS DEPLOYMENT SCRIPT "
echo "=========================="
echo ""
echo "Requirements:"
echo "    - Java environment"
echo "    - Ruby Gems"
echo "        - sass (3.2.1)"
echo "        - compass (0.12.2)"
echo "        - html5-boilerplate (2.1.0)"
echo "        - compass-h5bp (0.0.5)"
echo ""

#read -p "Press any key to continue... "

echo ""
echo "Recompiling SASS files"
echo "----------------------"

eval $PROGPATH/../css/recompile.sh

echo ""
echo "CSS minification"
echo "----------------"

LIST=$(find $PROGPATH/../css/sass -type f -maxdepth 1 -name "*.scss" -print)

for path in $LIST
do

	arr=$(echo $path | tr "/" "\n")

	for x in $arr
	do
		LAST=$(echo $x | sed -e "s/\.scss$/\.css/g")
	done

    eval "java -jar $PROGPATH/yuicompressor.jar $PROGPATH/../../wwwroot/css/$LAST -o $PROGPATH/../../wwwroot/css/$LAST --charset utf-8 --line-break 500"

    catfile=$PROGPATH/../../wwwroot/css/$LAST
    echo "/*!
 Page main styles | (c) Living Group
*/
$(cat $catfile)" > $catfile

	echo "  --> /wwwroot/css/$LAST - done"

done

echo ""
echo "Recompiling JS files"
echo "--------------------"

eval $PROGPATH/../js/compile.sh

echo ""
echo "JS minification"
echo "---------------"

LIST=$(find $PROGPATH/../js/scripts -type f -maxdepth 1 -name "*.js" -print)

for path in $LIST
do

	arr=$(echo $path | tr "/" "\n")

	for x in $arr
	do
		LAST=$x
	done

    eval "java -jar $PROGPATH/yuicompressor.jar $PROGPATH/../../wwwroot/js/$LAST -o $PROGPATH/../../wwwroot/js/$LAST --charset utf-8 --line-break 500 --preserve-semi"

    catfile=$PROGPATH/../../wwwroot/js/$LAST
    echo "/*!
 Page main APP (c) Living Group
*/
$(cat $catfile)" > $catfile

	echo "  --> /wwwroot/js/$LAST - done"

done

echo ""