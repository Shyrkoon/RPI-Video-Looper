#!/bin/bash
# This is the script that manages the schedules via crontab
# This script MUST NOT be executed by sudo

# First we save the parameters that we get from the web page

action=$1
name=$2
min=$3
hour=$4
folder=$5

# These are some needed variables
create="create"
delete="delete"

# Get the user that is executing the script
usr=$(whoami)

# This script will not work with the user sudo
sudo="sudo"
if [ $usr == $sudo ]; then
	exit
fi

# First we need to create the folder where all the cron scripts will be stored
mkdir -p /home/$usr/cron

# Check the action

if [ $action == $create ]; then

	# Check if the bash that we need to set already exists
	if [ ! -f /home/$usr/cron/$name.sh ]; then

	    # Creating the script that will be executed in the crontab
temp1="systemctl stop loop.service
\nsudo kill -6 $(pgrep -f omxplayer)
\nsudo kill -6 $(pgrep -f startvideo.sh)
\nsudo /bin/bash /home/$usr/scripts/startvideo.sh start /home/$usr/videos/$folder/"

   	 # Now we redirect it into a script
		echo -e $temp1 > /home/$usr/cron/$name.sh

   	 # And give execute permision to that script
		chmod +x /home/$usr/cron/$name.sh

    	# Check if the new schedule already exists on crontab
    	# If it exists it'll be deleted
		# This may be modified in the future
		crontab -l | grep -v $name | crontab -

    	# Creating new script with the time sets
		temp2="$min $hour * * * /home/$usr/cron/$name.sh"
		crontab -l | { cat; echo "$temp2"; } | crontab -
	else
		# If the time already exist, it'll be deleted and then created again
	    	sudo sed -i "/\b\($name\)\b/d" "/var/spool/cron/crontabs/$usr"
    		# crontab -l | grep -v '/home/$usr/cron/$name.sh' | crontab -

		#Create the updated timer
		tmp="$min $hour * * * /home/$usr/cron/$name.sh"
		crontab -l | { cat; echo "$tmp"; } | crontab -
		echo "modified"
	fi
fi

if [ $action == $delete ]; then
	# Here we will delete the schedule

	# Delete the script
	rm -rf /home/$usr/cron/$name.sh

	# Delete the cron entry
	sudo sed -i "/\b\($name\)\b/d" "/var/spool/cron/crontabs/$usr"

fi
