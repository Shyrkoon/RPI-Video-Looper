# Download all the scripts from GitHub #

#Check that there are no scripts running in the background
systemctl stop loop.service

kill -6 $(pgrep -f omxplayer)
kill -6 $(pgrep -f startvideo.sh)

#Now that the running scripts have been stopped, we'll delete all of them to download the new ones
rm -rf /home/$newuser/scripts

#Move to the user home
cd /home/$newuser

#Download all necessary scripts from GitHub
wget https://github.com/Shyrkoon/RPI-Video-Looper/blob/master/Scripts/scripts.rar?raw=true

#Rename the folder because it gets downloaded with a weird name
mv scripts.zip\?raw\=true scripts.zip

#Un pack all the scripts
unzip scripts.zip

#Remove the tar
rm scripts.zip

#Change the owner of all the scripts and give execute permission
chown -R $newuser /home/$newuser/scripts
chmod +x /home/$newuser/scripts
