# Raspberry Pi Video Looper

## Scripts Explanation
This readme has an extensive explanation of every script.
The scripts are heavily commented with thorough explanations.


## Installer
This is the main script. As you can see on the README of the previous page.

Keep in mind that some lines of code inside the script need to be changed if you have your own scripts in your GitHub and if you want to access to your own database.

This script will make the following changes:
* Check if the script is being executed by sudo and in the correct linux distro (Must be Raspbian)
* Update and upgrade the system
* Install all the necessary packages
* Enable VNC for remote administration
* Create a new user (The new user's name can be changed at the start of the script)
* Give sudo priviledges to this new user
* Download all the other needed scripts from GitLab or GitHub
* Create a service to run the video looper manually
* Create another service to auto-loop all the videos located in /videos/ (It is just a default folder)
* Hide the task bar and the recycle bin, disable bluetooth and set to autologin to the new user
* Revoke ssh acces to the user pi and set the new user as the only one that can access to the Raspberry pi remotely
* Register the Raspberry into the database of the Web Page
* Remove all the sudo and admin priviledges of the user PI
* Reboot the system

---

Let's take a look inside the script.

In the first part of the script we can find some color variables. These are just to make the 
exit in the console much smoother and if the script stops working, to be able to check where did it stop.
Then we found a basic check to see if the script is being executed by sudo and in Raspbian.

Then we see the variables that will be used a lot in the script. It is highly recomended to change a least the password after the installation. 

```
#New user that will be created
newuser="project"
#New Password
pwd="project2018"
```

---
Then the script updates the system and installs the following packages:
* youtube-dl
* omxplayer
* wget
* chromium-browser
* mysql-client

youtube-dl is the program that downloads videos from a very extensive list of pages.

Cromium browser will not be used, but it's nice to have updated in case you want to access into
the web page in the Raspberry. wget will be used to download the scripts from GitLab or GitHub. 

omxplayer is the program that plays video on the Raspberry pi. We found that this one works extremely better than the others in the RPI.

Finally mysql-client is needed to make a connection to the database to register the Raspberry.


After that, the next lines enable ssh to manage the Raspberery and VNC Server just in case you
need to manage the system from another computer. Again, we highly recomend to change the
password of your Raspberry, because these services will be enabled after the execution of the
script.

---
Afer installing all necessary packages, the script makes the following changes to the new user:
* Check if the new user already exists
    * If not exists, it will be created
* Give power priviledges to the new user, so it can reboot and poweroff the system without needing password

Then the script creates the next service to set autologin into the new user:
```bash
[Service]
ExecStart=-/sbin/agetty --autologin $newuser --noclear %I 38400 linux
```
The service gets saved in /etc/systemd/system/getty@tty1.service.d/autologin.conf

For the new user to be able to autolog into the system some changes are needed i
 /etc/lightdm/lightdm.conf. These are the two lines that need to be modified:

```
autologin-user=
autologin-user-timeout=
```

These are the two lines modified:
```
autologin-user = project
autologin-user-timeout = 0
```

---
The script now downloads all the other scripts from GitHub. Basically just donwloads a zipped 
file, changes the name of that file because it get donwloaded with a weird name, unzips it into 
the home of the new user and deletes the .zip that has been downloaded.

---
The following part it is not 100% necessary, but it gets created to manualy start the script 
that starts playing the videos on loop. 
The next service gets created to start playing videos via systemctl.
```
[Unit]
Description=Video Looper
Documentation=https://gitlab.com/j.torrents/projecte

[Service]
Type=simple
User=$newuser
ExecStart=/bin/bash /home/$newuser/projecte/startvideo.sh
Restart=always
RestartSec=2

[Install]
WantedBy=user.target
```

To manually start playing videos (located in a default folder) execute this command in the 
terminal:

``` systemctl start loop.service ```

---
After creating the service, some changes are made to the desktop. First Bluetooth gets disabled 
because it is enabled after installing Raspbian for the first time.

Then the Recycle Bin and the Task Bar will get hidden because it is visually unattractive to see 
them after the user logs or if the video player crashes. It will not be very appealing for the 
people watching the videos in that moment.

---
Now the script changes parameters againg from the user Pi. It checks if Pi exists on 
/etc/sudoers first because if it is not present it won't do anything. If Pi exists in the 
sudoers file it will get deleted with the command 'sed'.

We enabled ssh service before, but that means that every user that gets created can access via 
ssh. That can be changed deleting everything everything in:

``` /etc/ssh/sshd_config ```

And then adding just one user:

``` AllowUsers {new-user} ```

Now the only user that will be able to access via ssh will be the new user created at the start 
of the script.

Pi also has a special file in ``` /etc/sudoers.d/010_pi-nopasswd ``` that let's the user Pi do 
everything in the Raspberry without needing it's password. This should be deleted in any 
Raspberry if you plan to be using your own user.
The user Pi can not be deleted because it forms part of at least fifteen different groups. It is 
not required to delete Pi from all of them, we just need to take him out from 'sudo' and 'adm'.
It can be made with the following commands:
```
deluser {new-user} sudo
deluser {new-user} adm
```

---
For the final part the script creates a service in ``` /home/$newuser/.config/autostart/.desktop ``` 
to execute two scripts that need to be executed as soon the user logs into the system.
One of them checks every few seconds if the address has changed. If it has changed, the script 
will update it on the database in AWS. The other script starts playing videos from a default 
folder specified in the startvideo.sh script. This is the service created:

```
[Desktop Entry]
Type=Application
Name=Video Looper
Exec=sudo /bin/bash /home/project/scripts/services.sh
Comment=Starts omxplayer on loop
X-GNOME-Autostart-enabled=true
```

The script will also create a service to manually execute the prior code manually. It does not 
need to be created, but it is very handy if you want to execute the code manually in the future.
It can be executed with the folloing code:
``` systemctl start / restart publicIP.service ```

Finally the script gets the current IP and the MAC of the Raspberry and sends it tothe Database 
to registed the Raspberry. 

Very Important:
As you can see in the following sentence:

```sudo mysql -h 34.217.243.108 -u rpiClient -prpiClients1 projecteRPI --ssl-key=/home/$newuser/scripts/Projecte.pem <<EOF```
a .pem is being used. This needs to be created manually by you to establish connection to te 
database in the servers of AWS. This part of the script will not work automatically if the key 
it is not present in the zip that gets downloaded at the start. 

Please have a .zip with all the necessary scripts and keys before executing this one.


## Video Downloader
The name of this script is oficially ``` donwloader.sh```. This script uses youtube-dl to 
download videos to the Raspberry. The videos will be downloaded with the best quality available 
from youtube.

This script is being used automatically by the web page to download videos, but it can be 
executed manually. 

There are the three variables needed in order:
* link (Youtube URL)
* location (location where the video will be downloaded)
* name (the vide will be renamed after the download)


## Delete_Video
This is a simple script to delete files from a system. It uses the ``` rm ``` command to delete 
the video. 

It has two variables:
* location (location of the video)
* name (name on the video) 

This script, as the other one, is being managed in the webpage.


## Scheduler
This one manages the schedules that can be made in the webpage.

First of all it has five variables:
* action (create or delete)
* name (name given to the schedule)
* min (minute)
* hour (hour)
* folder (folder that will be looped)

Then the script gets wich user is executing the script, because every user has a diferent cron 
and you ight have changed the name of the user that gets created in the installer. This script 
MUST NOT BE executed by sudo. The script fails if it gets executed with sudo because the cron 
file gets saved on the sudo folder.

For every schedule, it creates a custom script that will be in the cron settings. This scripts 
stops playing video on the Raspberry and starts the same script (startvideo) with the folder 
defined in the previous variable. Example:
```
systemctl stop loop.service
\nsudo kill -6 $(pgrep -f omxplayer)
\nsudo kill -6 $(pgrep -f startvideo.sh)
\nsudo /bin/bash /home/$usr/scripts/startvideo.sh start /home/$usr/videos/$folder/
```

Then the scripts saves it into ```/home/{user}/cron/{name}.sh ```. After doing this, the line of 
the crontab gets created with this format:
``` $min $hour * * * /home/$usr/cron/$name.sh ```.

All of this is done only if the initial variable is set to create. If it is set to delete, the 
script will delete the line of the cron that has the name of the name variable. 

Aditionally, if you try to create a new schedule with a name that already exists, it will simply 
be modified in the cron file.


## Script Downloader 
This script is here just to be executed manually if you don't want to execute the entire ``` installer.sh ``` 
just to update the scripts. It has the same code as the installer.


## Startvideo
The most important script of all. This is the script that plays every video on loop.

First of all we have the following variables:
* LOOP_FOLDER=$2 # Saves the location in a variable
* LOOP_STATUS=$1 # Desired state of the script
* START="start" # Start variable
* EMPTY="" # This is just an empty variable

Then a default folder is set up if the script gets called without arguments. This folder can be 
changed simply modifying this line:
``` LOOP_FOLDER="/path/" ```

After setting the folder, a basic if gets executed to check for the status. If it's set to START 
or if it's empty the script will get executed, but if it set to STOP or any other thing, the 
script will be stopped if it is already on execution.

The following lines of code are from a script already made by Tim Schwartz. The script has 
premade comments. If you need more help, visit his [GitHub](https://github.com/timatron/videolooper-raspbian).


## UpdateCheckIP
This script must be executed in the background. It will be executed after the user logs into the 
system. 

It checks the private ip of the system every 60 seconds and checks if its the same that is 
registered on the Database. If it has changed it will make an update.

Remember to change the IP, user and password from the script to match your Database. 

## UpdateCheckPublicIP
Same as the previous script but this one checks the public ip of the system. This one needs to 
be used if you have your web server in an AWS instance. Port forwarding will be needed to access 
to the Raspberry from the web server.

To change between this script and the second one simply change them in ``` services.sh ```.

Remember to change the IP, user and password from the script to match your Database.

## Services
This last script gets managed by the ``` installer.sh ```. It gets executed everytime the user 
logs into the system. There are two paths to two different scripts that need to be executed on 
loop. One checks if the ip of the Raspberry has changed and if so it gets modified on the 
Database. The other script is the ```startvideo.sh ```which starts playing videos on loop. 

---
Thanks for reading.
