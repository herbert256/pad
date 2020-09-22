#!/usr/bin/env bash

sudo dpkg --configure -a
sudo apt -y -f install

sudo apt update
sudo apt -y upgrade
sudo apt -y full-upgrade
sudo apt -y autoremove
sudo apt -y clean
sudo apt -y autoclean
