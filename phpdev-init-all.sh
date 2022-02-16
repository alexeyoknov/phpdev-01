#!/bin/bash

GIT_HTTPS="https://github.com/alexeyoknov/phpdev-01.git"
GIT_SSH="git@github.com:alexeyoknov/phpdev-01.git"
GIT_USER_NAME="Alexey Oknov"
GIT_USER_EMAIl="pitrider@mail.ru"

if [ $(id -g) -eq 0 ]; then
    echo "Run this script as user"
    exit
fi

sudo apt-get update && apt-get -y upgrade 

echo "Installing base utilities"
sudo apt install -y gcc make perl ssh git curl htop mc zsh

echo "Installing web stack"
sudo apt-get install -y nginx mysql-server php-fpm php-intl php-mysql

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

echo "Fixing mysql"
sudo mysql -u root -e "UPDATE mysql.user SET plugin = 'mysql_native_password', authentication_string  = '' WHERE user = 'root';"
sudo service mysql restart

sudo mkdir /media/mycd
sudo mount /dev/sr0 /media/mycd

if [ -f /media/mycd/VBoxLinuxAdditions.run ]; then
    sudo /media/mycd/VBoxLinuxAdditions.run
fi

sudo umount /media/mycd

echo "Fixing some rights"

name=""
echo "Your name is ${name}"
if [ -z $(id -nG | grep www-data) ]; then

    name=$(whoami)
    sudo usermod -aG www-data,vboxsf ${name}

fi
if [ ! -d /www ]; then 
	sudo mkdir /www
fi

if [ -d /www ]; then
    chmod 777 -R /www
    cd /www
    git clone ${GIT_HTTPS}

    cd ./phpdev-01
    git remote set-url origin ${GIT_SSH}

    sudo chmod g+w,o-w -R /www
    sudo chown www-data:www-data -R /www

    sudo ln -sf /www/phpdev-01/conf/pma.conf /etc/nginx/sites-enabled/pma.conf
    sudo ln -sf /www/phpdev-01/conf/local.nginx /etc/nginx/sites-enabled/phpdev.conf

    sudo service nginx reload
fi

echo "Installing VSCode"

sudo apt install -y software-properties-common apt-transport-https wget
sudo wget -qO- https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > packages.microsoft.gpg
sudo install -o root -g root -m 644 packages.microsoft.gpg /etc/apt/trusted.gpg.d/
sudo sh -c 'echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/trusted.gpg.d/packages.microsoft.gpg] https://packages.microsoft.com/repos/code stable main" > /etc/apt/sources.list.d/vscode.list'
sudo rm -f packages.microsoft.gpg

sudo apt update
sudo apt install -y code


echo "Installing oh-my-zsh"
sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"

echo "Generating ssh key for git"
if [ ! -f ~/.ssh/id_rsa.pub ]; then
	ssh-keygen -t rsa -q -f "$HOME/.ssh/id_rsa" -N ""
fi

echo "Export this SSH PUBLIC KEY to git"
cat ~/.ssh/id_rsa.pub

# Git config
echo "Git global config"
git config --global user.name "${GIT_USER_NAME}"
git config --global user.email "${GIT_USER_EMAIl}"

echo "Adding pma & dev01 to /etc/hosts"
b=$(grep -F pma.my /etc/hosts)
if [ -z "$b" ]; then 
	echo "127.0.0.1 pma.my" | sudo tee -a /etc/hosts
fi

b=$(grep -F dev01.my /etc/hosts)
if [ -z "$b" ]; then 
	echo "127.0.0.1 dev01.my" | sudo tee -a /etc/hosts
fi

if [ -n "${name}" ]; then
    echo "Need relogin"
    #sudo pkill -KILL -u ${name}
fi
echo "If new VirtualBox modules has been installed, please, reboot your system"

