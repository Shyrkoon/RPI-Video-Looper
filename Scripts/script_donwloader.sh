#!/bin/bash
#Esto es una prueba para ver si puedo hacer que se descargue todos los scripts de nuevo

#Este es el usuario con el que estamos trabajando de momento
newuser=project

# Download all the scripts from GitLab or GitHub
#First we need to check that there are no scripts running in the background
systemctl stop loop.service

kill -6 $(pgrep -f omxplayer)
kill -6 $(pgrep -f startvideo.sh)

#Now that the running scripts have been stopped, we'll delete all of them to download the new ones
rm -rf /home/$newuser/scripts

#Move to the user home
cd /home/$newuser

#Download all necessary scripts from GitLab
wget https://github.com/Shyrkoon/Base-de-dades/blob/master/proejcte/scripts.zip?raw=true

#Rename the folder because it gets downloaded with a weird name
mv scripts.zip\?raw\=true scripts.zip

#Un pack all the scripts
unzip scripts.zip

#Remove the tar
rm scripts.zip

#Change the owner of all the scripts
chown -R $newuser /home/$newuser/scripts
