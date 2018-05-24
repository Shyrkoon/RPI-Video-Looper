#!/bin/bash
# Script made by Raúl González and Jose Alberto Torrents
# This script does a basic check, installs all the necessary programs and sets the user pi and the new user to a desired state


#Set Color Variables
RED="\033[0;31m"
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
NC="\033[0m"       #No color


#Check if this script is being executed by sudo
#If this script isn't executed by a super user, it will not work as intended
if [ "$EUID" -ne 0 ]
  then echo -e "\n${RED}Please run as root${NC}\n"
  exit
else
  echo -e "\n${GREEN}This script is being excecuted by sudo${NC}\n"
fi

#Check if this script is being executed on Raspbian
if (grep -R "Raspbian" /etc/*-release > /dev/null)
then
	echo -e "${GREEN}This script is being executed on the correct OS Distribution${NC} \n"
else
	echo -e "${RED}This script cannot be executed on your system${NC}"
	exit
fi


#User that wil be modified (Default user = pi)
user="pi"
#Default group (will be created if not exists)
grp="pi"

#New user that will be created
newuser="project"
#New Password
pwd="project2018"


#  Update all dependencies  #

#Update and upgrade the system
apt-get update -y
apt-get upgrade -y

#Installing video downloader (youtube-dl)
apt-get install -y youtube-dl

#Install omxplayer
#This is the video player that will be used to play and loop videos
apt-get install -y omxplayer

#Install wget
apt-get install -y wget

#Install chromium-browser, just in case if it isn't installed on the Raspberry
apt-get install -y chromium-browser

#Install mysql-client, needed to register this Raspberry in the database
apt-get install -y mysql-client

#Enable SSH
systemctl enable ssh
systemctl start ssh
	echo -e "\n${GREEN}SSH is now enabled${NC}\n"

#Enable VNC Server
systemctl enable vncserver-x11-serviced.service
systemctl start vncserver-x11-serviced.service
	echo -e "${GREEN}VNC is now enabled${NC}\n"


# Check if the new user exists
# If not, then proceed to create that user

echo -e "${YELLOW}Checking users${NC}"

if getent passwd $newuser > /dev/null 2>&1; then
	echo -e "${GREEN}User $newuser already exists ${NC}\n"
else
        echo -e "${YELLOW}The user $newuser does not exist${NC}"
        echo -e "${YELLOW}Creating user $newuser${NC}"
        mkdir -p /home/$newuser
	useradd -g sudo -d /home/$newuser -s /bin/bash "$newuser"
	echo $newuser:$pwd | chpasswd
	echo -e "${GREEN}User $newuser created${NC}\n"
fi

#Change home owner to Nuvify
chown -R $newuser /home/$newuser


# Give to the new user power provileges
# First we will check if the user already exists in the sudoers file

if  grep -R $newuser /etc/sudoers > /dev/null
then
	echo -e "${GREEN}User $newuser exists in sudoers list and already has power privileges${NC}"
else
	#After checking that the user doesn't have power privileges, we will add them into the sudoers file
	echo -e "${YELLOW}Can't find user $newuser in sudoers list. Adding user with power privileges${NC}"
		nuvipwr="$newuser ALL=(ALL) NOPASSWD: /sbin/poweroff, /sbin/reboot, /sbin/shutdown"
		echo $nuvipwr >> /etc/sudoers
	echo -e "${GREEN}Privileges granted to user $newuser ${NC}\n"
fi


#Set autologin into the new user
#In Raspbian we need to make a new service and execute it on start up

autolograsp="[Service]
\nExecStart=
\nExecStart=-/sbin/agetty --autologin $newuser --noclear %I 38400 linux"

	#First the autoligin needs to be enabled on the lightdm config
	echo -e "${YELLOW}Changing autologin settings...${NC}"
	    sed -i '/autologin-user=/c\autologin-user = '"$newuser" /etc/lightdm/lightdm.conf
	    sed -i '/autologin-user-timeout=/c\autologin-user-timeout = 0' /etc/lightdm/lightdm.conf

	    #Then we will create a new service that will be executed on boot after login into any user
	    echo -e $autolograsp > /etc/systemd/system/getty@tty1.service.d/autologin.conf
	    systemctl enable getty@tty1.service

	    #We need to reconfigure lightdm after making the changes
	    dpkg-reconfigure lightdm

	echo -e "${GREEN}Autoling settings modified. The system will autolog into $newuser \n${NC}"


# Download all the scripts from GitLab #

#Check that there are no scripts running in the background
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

#Change the owner of all the scripts and give execute permission
chown -R $newuser /home/$newuser/scripts
chmod +x /home/$newuser/scripts

#Create loop.service
#This service manages the script that puts the video on loop

	echo -e "${YELLOW}Creating Loop service${NC}"
scrp="
[Unit]
\nDescription=Video Looper
\nDocumentation=https://gitlab.com/j.torrents/projecte
\n
\n[Service]
\nType=simple
\nUser=$newuser
\nExecStart=/bin/bash /home/$newuser/projecte/startvideo.sh
\nRestart=always
\nRestartSec=2
\n
\n[Install]
\nWantedBy=user.target"

echo -e $scrp > /lib/systemd/system/loop.service
	echo -e "${GREEN}Loop service has been created${NC}\n"

# Reload Daemon (to use the service without needing to reboot)
systemctl daemon-reload
	echo -e "${GREEN}Daemon Reloaded${NC}\n"

#Init daemon at start up
systemctl enable loop.service
	echo -e "${GREEN}Service enabled on system start${NC}\n"


#Bluetooth usually comes enabled on the Raspberry Pi
#Disable bluetooth on the Raspberry Pi

bt="dtoverlay=pi3-disable-bt"
echo -e "${YELLOW}Deactivating bluetooth${NC}"
if (grep -R $bt /boot/config.txt > /dev/null)
then
    echo -e "${GREEN}Bluetooth is currently disabled${NC}\n"
else
    echo $bt >> /boot/config.txt
    echo -e "${GREEN}Bluetooth has been disabled${NC}\n"
fi


#Hide the taskbar and the recycle bin
#To make the desktop nicer on startup
routetb="/home/$newuser/.config/lxpanel/LXDE-pi/panels/panel"
routerb="/home/$newuser/.config/pcmanfm/LXDE-pi/desktop-items-0.conf"
hide="autohide=0"
height="heightwhenhidden=2"

echo -e "${YELLOW}Hiding the taskbar and the recycle bin after the next reboot${NC}"
        sed -i "/$hide/c\autohide=1" $routetb
        sed -i "/$height/c\heightwhenhidden=0" $routetb
        sed -i "/show_trash=1/c\show_trash=0" $routerb
echo -e "${GREEN}Done${NC}\n"


#Remove default user (pi) privileges from sudoers file
	echo -e "${YELLOW}Checking if $user has privileges${NC}"
if (grep -R $user /etc/sudoers > /dev/null)
then
	sed -i "/\b\($user\)\b/d" "/etc/sudoers"
	echo -e "${GREEN}Sudo privileges have been removed${NC}\n"
else
	echo -e "${GREEN}User $user does not have privileges${NC}\n"
fi


#Leave only the new user as allowed in the sshd.conf file
#The new user will be the only one that can log into the system with shh
tmp="AllowUsers $newuser"

	echo -e "${YELLOW}Allowing selected users to log into in using ssh${NC}"
if (grep -R "AllowUsers" /etc/ssh/sshd_config > /dev/null)
then
	sed -i "/\b\(AllowUsers\)\b/d" "/etc/ssh/sshd_config"
	echo $tmp >> /etc/ssh/sshd_config
	echo -e "${GREEN}Users allowed:${NC}"
else
	echo $tmp >> /etc/ssh/sshd_config
	echo -e "${GREEN}Only the user $newuser may access into the system using ssh${NC}"
fi


#Remove privileges from custom pi file
#This will remove the ability to access to any user withput using passwords
	echo -e "\n${YELLOW}Removing 'NOPASSWD' privileges from user $user ${NC}"
	    rm -f /etc/sudoers.d/010_pi-nopasswd
	echo -e "${GREEN}NOPASSWD privileges have been removed from the user $user ${NC}\n"

#Change primary group from default user
#Create default group
if grep -q $grp /etc/group
then
         echo -e "${GREEN}Group $grp already exists${NC}"
else
	addgroup $grp
fi

#Modify user primary group
usermod -g $grp $user

#Change default password
echo "pi:$pwd" | chpasswd

#Remove the user from the sudo and adm groups
	echo -e "\n${YELLOW}Removing user $user from sudo and adm groups${NC}"
deluser $user sudo
deluser $user adm
	echo -e "${GREEN}User $user is no longer member of group sudo${NC}\n"

#The next part of the script creates a service that gets executed after the user has loged in into his account
#It needs to be made this way because using the "enable", the service tries to load before the user logs into the system
echo -e "${GREEN}Making changes in the .desktop to execute 2 scripts on boot${NC}"

SERVICES="
\n[Desktop Entry]
\nType=Application
\nName=Video Looper
\nExec=sudo /bin/bash /home/project/scripts/services.sh
\nComment=Starts omxplayer on loop
\nX-GNOME-Autostart-enabled=true"

echo -e $SERVICES > /home/$newuser/.config/autostart/.desktop
echo -e "${GREEN}.desktop file has been modified${NC}"

# Make the service that will manage the connection between the Raspberry and the Server
# The script needs to be in the system before executing the service. It should be downloaded automatically
# This does not need to be a service at all, but in the future it will be easier to execute systemctl restart publicIP.service than to manually execute the script
	echo -e "${YELLOW}Creating publicIP service${NC}"
DBService="
[Unit]
\nDescription=Connection to the Web Server
\nDocumentation=https://gitlab.com/j.torrents/projecte
\n
\n[Service]
\nType=simple
\nUser=$newuser
\nExecStart=/bin/bash /home/$newuser/projecte/updateCheckPublicIP.sh
\nRestart=always
\nRestartSec=2
\n
\n[Install]
\nWantedBy=user.target"

echo -e $DBService > /lib/systemd/system/publicIP.service
	echo -e "${GREEN}publicIP service has been created${NC}\n"

# Reload Daemon
systemctl daemon-reload
	echo -e "${GREEN}Daemon Reloaded${NC}\n"

#Init daemon at start up
systemctl enable publicIP.service
	echo -e "${GREEN}Service enabled on system start${NC}\n"


# The next part only needs to be executed one time
#!/bin/bash
read mac < <(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/address)
read publicIP < <(curl -s http://whatismyip.akamai.com/)
echo -e "${GREEN}$mac ${NC}"
echo -e "${GREEN}$publicIP ${NC}\n"
sudo mysql -h 34.217.243.108 -u rpiClient -prpiClients1 projecteRPI --ssl-key=/home/$newuser/scripts/Projecte.pem <<EOF
INSERT INTO raspberries(mac,ipPublica) VALUES("$mac","$publicIP");
EOF


#Reboot System to apply changes
echo -e "${GREEN}The system will be restarted in 10 seconds${NC}"
echo -e "${YELLOW}If you still need to close aplications, press CTRL + C to cancel the reboot${NC}"
sleep 10
reboot
