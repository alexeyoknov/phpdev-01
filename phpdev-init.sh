#!/bin/bash

if [ `id -g` -gt 0 ]; then
    echo "Run this script as root"
    exit
fi

apt-get update && apt-get -y upgrade 

echo "Installing base utilities"
apt install -y gcc make perl git curl htop mc zsh 

echo "Installing web stack"
apt-get install -y nginx mysql-server php-fpm php-intl php-mysql

echo "Fixing mysql"
if [ -f ./db-fix.sql ]; then
    mysql -u root < ./db-fix.sql
    systemctl restart mysql.service
fi

echo "Installing PMA"
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password $APP_PASS" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password $ROOT_PASS" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password $APP_DB_PASS" | debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect" | debconf-set-selections
apt install -y phpmyadmin

echo "Fixing phpmyadmin"
cp /etc/phpmyadmin/config.inc.php /etc/phpmyadmin/config.inc.php.old
sed -i '/AllowNoPassword/s/\/\///' /etc/phpmyadmin/config.inc.php

ln -s /www/phpdev-01/conf/pma.conf /etc/nginx/sites-enabled/pma
ln -s /www/phpdev-01/conf/local.nginx /etc/nginx/sites-enabled/phpdev

echo "\n\n###" | tee -a /etc/hosts
echo "127.0.0.1\tpma.my" | tee -a /etc/hosts
echo "127.0.0.1\tdev01.my" | tee -a /etc/hosts

echo "Installing VSCode"

apt install -y software-properties-common apt-transport-https wget
wget -qO- https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > packages.microsoft.gpg
install -o root -g root -m 644 packages.microsoft.gpg /etc/apt/trusted.gpg.d/
sh -c 'echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/trusted.gpg.d/packages.microsoft.gpg] https://packages.microsoft.com/repos/code stable main" > /etc/apt/sources.list.d/vscode.list'
rm -f packages.microsoft.gpg

apt install -y apt-transport-https
apt update
apt install -y code


echo "Installing oh-my-zsh"
sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"

