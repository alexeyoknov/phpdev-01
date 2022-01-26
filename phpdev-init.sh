#!/bin/zsh

echo "Installing mysql/php/php-fpm/git/curl/zsh"
apt-get install -y mysql-server
apt-get install -y php-fpm phpmyadmin git curl zsh

echo "Fixing mysql"
if [ -f ./db-fix.sql ]; then
    mysql -u root < ./db-fix.sql
    systemctl restart mysql.service
fi

echo "Fixing phpmyadmin"
sed -i '/AllowNoPassword/s/\/\///' /etc/phpmyadmin/config.inc.php

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

