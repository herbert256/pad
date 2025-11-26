#!/bin/bash

rm -rf /home/herbert/pad/data
rm -rf /home/herbert/pad/www/data

mkdir /home/herbert/pad/data
ln -s /home/herbert/pad/data /home/herbert/pad/www/data

chown herbert:herbert /home/herbert/pad/data
chown herbert:herbert /home/herbert/pad/www/data
