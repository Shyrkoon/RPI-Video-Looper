#Basic script that gets executed after the user logs into the systemd

# This first script gets executed in the background and checks if the IP has changed
/bin/bash /home/project/scripts/updateCheckPublicIP.sh &

# This one executes the script that loops the videos. It gets executed everytime the user logs into the Raspberry
# It loops a default folder (/videos). This folder can easilly be changed inside of the script
/bin/bash /home/project/scripts/startvideo.sh
