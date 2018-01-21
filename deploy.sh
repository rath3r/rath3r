#!/bin/bash

declare -a files=("about.html" "animate.html" "index.html" "maps.html" "scripts" "styles" "data")

for i in "${files[@]}"
do
    rm -rf rath3r.github.io/$i
    cd dist
    cp -R $i ../rath3r.github.io/$i
    cd ..
done

echo "Complete"
