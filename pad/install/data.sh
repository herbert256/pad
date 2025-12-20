#!/bin/bash

rm -rf /home/herbert/pad/DATA
rm -rf /home/herbert/pad/www/DATA

mkdir /home/herbert/pad/DATA
ln -s /home/herbert/pad/DATA /home/herbert/pad/www/DATA

chown herbert:herbert /home/herbert/pad/DATA
chown herbert:herbert /home/herbert/pad/www/DATA
