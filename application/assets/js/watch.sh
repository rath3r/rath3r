#!/bin/sh

PROGPATH=$(dirname $_)

prevsum=na
newsum=na
firststart=yes

echo "\033[m"
echo ">>> Watching for changes in '$PROGPATH/' folder."
echo "    Press Ctrl-C to Stop."
echo ""

watch() {

LIST=$(find $PROGPATH/ -type f -name "*.js" -print)

dirsum1=``

for path in $LIST
do
	chsum=`md5 $path`

	arr=$(echo $chsum | tr "=" "\n")
	for x in $arr
	do
	LAST=$x
	done

	dirsum1=$dirsum1$LAST
done
newsum=$dirsum1

if [ $newsum == $prevsum ] ; then 
		sleep 0 ; 
	else 
		
		if [ $firststart == yes ] ; then 
				echo "\033[m>>> \033[34mInitial startup\033[1;30m" ; 
			else
				changetime=$(date '+%X')
				echo "\033[m>>> \033[32mChange detected\033[m at $changetime\033[1;30m" ; fi
				
		firststart=no
		
		eval $PROGPATH/compile.sh
		echo "\033[m" ; fi

prevsum=$newsum

sleep 2

watch $*

}

watch $*changetime
