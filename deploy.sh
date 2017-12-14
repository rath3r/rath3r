#!/bin/bash

declare -a files=("about.html" "animate.html" "index.html" "maps.html" "scripts" "styles")

cd rath3r.github.io

git status

cd ..
for i in "${files[@]}"
do
    rm -rf rath3r.github.io/$i
    cd dist
    cp -R $i ../rath3r.github.io/$i
    cd ..
done

cd rath3r.github.io
git status

