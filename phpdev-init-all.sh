#!/bin/bash

if [ `id -g` -eq 0 ]; then
    echo "Run this script as user"
    exit
fi

sudo apt-get update && apt-get -y upgrade 

echo "Installing base utilities"
sudo apt install -y gcc make perl git curl htop mc zsh 

echo "Installing web stack"
sudo apt-get install -y nginx mysql-server php-fpm php-intl php-mysql

echo "Fixing mysql"
if [ -f ./db-fix.sql ]; then
    sudo mysql -u root < ./db-fix.sql
    systemctl restart mysql.service
fi

echo "Installing PMA"
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password $APP_PASS" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password $ROOT_PASS" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password $APP_DB_PASS" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect" | sudo debconf-set-selections
sudo apt install -y phpmyadmin

echo "Fixing phpmyadmin"
sudo cp /etc/phpmyadmin/config.inc.php /etc/phpmyadmin/config.inc.php.old
sudo sed -i '/AllowNoPassword/s/\/\///' /etc/phpmyadmin/config.inc.php

sudo mkdir /media/mycd
sudo mount /dev/sr0 /media/mycd
sudo /media/mycd/VBoxLinuxAdditions.run

sudo umount /media/mycd

echo "Fixing some rights"
sudo usermod -aG www-data,vboxsf $name
#sudo usermod -aG vboxsf $name
sudo chmod g+w -R /www

echo "Installing VSCode"

sudo apt install -y software-properties-common apt-transport-https wget
sudo wget -qO- https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > packages.microsoft.gpg
sudo install -o root -g root -m 644 packages.microsoft.gpg /etc/apt/trusted.gpg.d/
sudo sh -c 'echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/trusted.gpg.d/packages.microsoft.gpg] https://packages.microsoft.com/repos/code stable main" > /etc/apt/sources.list.d/vscode.list'
sudo rm -f packages.microsoft.gpg

sudo apt install -y apt-transport-https
sudo apt update
sudo apt install -y code


echo "Installing oh-my-zsh"
sudo sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"

