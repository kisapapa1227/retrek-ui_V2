#!/bin/sh
#
#
echo easy
b=sub1
git branch $b
git checkout $b
git add .
git commit -m "[Add] index"
git push origin $b
