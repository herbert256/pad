#!/bin/bash

rm -rf /home/herbert/data
rm -rf /home/herbert/www/data

mkdir /home/herbert/data
ln -s /home/herbert/data /home/herbert/www/data

chown herbert:herbert /home/herbert/data
chown herbert:herbert /home/herbert/www/data
