#!/bin/bash

if [ `id -g` -eq 0 ]; then
    echo "Run this script as user"
    exit
fi

name=`whoami`

echo $name

echo "Fixing some rights"
sudo usermod -aG www-data,vboxsf $name
#sudo usermod -aG vboxsf $name
sudo chown www-data:www-data -R /var/www
sudo chmod g+w -R /var/www

echo "Generating ssh key for git"
ssh-keygen -t rsa -q -f "$HOME/.ssh/id_rsa" -N ""
cat ~/.ssh/id_rsa.pub

# Git config
echo "Git config"
git config --global user.name "Alexey Oknov"
git config --global user.email "pitrider@mail.ru"

