#!/bin/bash
if [ `id -g` -eq 0 ]; then
    echo "Run this script as user"
    exit
fi

name=`whoami`

echo $name

echo "Generating ssh key for git"
if [ ! -f ~/.ssh/id_rsa.pub ]; then
	ssh-keygen -t rsa -q -f "$HOME/.ssh/id_rsa" -N ""
fi

echo "Export this SSH PUBLIC KEY to git"
cat ~/.ssh/id_rsa.pub

# Git config
echo "Git config"
git config --global user.name "Alexey Oknov"
git config --global user.email "pitrider@mail.ru"


b=`fgrep pma.my /etc/hosts`
if [ -z "$b" ]; then 
	echo "127.0.0.1 pma.my" | sudo tee -a /etc/hosts
fi

b=`fgrep dev01.my /etc/hosts`
if [ -z "$b" ]; then 
	echo "127.0.0.1 dev01.my" | sudo tee -a /etc/hosts
fi


