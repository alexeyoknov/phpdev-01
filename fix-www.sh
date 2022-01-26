#!/bin/zsh

if [ `id -g` -eq 0 ]; then
    echo "This script run with user"
    exit
fi

name=`whoami`

echo $name

echo "Fixing /var/www rights"
sudo usermod -aG www-data $name
sudo chown www-data:www-data -R /var/www
sudo chmod g+w -R /var/www

echo "Generating ssh key for git"
ssh-keygen
cat ~/.ssh/id_rsa.pub
