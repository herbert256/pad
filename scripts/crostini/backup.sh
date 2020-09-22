#!/bin/bash

now1=$(date +"%Y-%m-%d")
now2=$(date +"%H_%M_%S")

mkdir -p ~/google/crostini/$now1

cd ~

tar zcf ~/google/crostini/$now1/$now2.tar.gz apps pad www scripts