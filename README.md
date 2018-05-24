# Raspberry Pi Video Looper
<img src="Web%20Interface%20Local/images/logo.png" width="100"/>

## Description

This project loops videos indefinitely located on a Raspberry Pi or on a USB. The RPI can be administrated by a webpage (if the raspberry and the server are located on the same VPN, on the same network or if your Router has port forwarding enabled ) or by that same web page located in the Raspberry pi. You can also execute the script that loops everything manually if you don't need fancy things
like a web page or schedules.

## Basic Features
* Loops videos from a USB on boot
* Loops videos from a folder
* Administrated by a web page. There is no need to do configuration changes directly into the RPI
* Web page with strong log in security
* Schedules can be configured on the web page to play different folders on different hours
* Posibility to see the videos that are being played and the videos located on other folders

## Requirements
Internet access is required to install all the scripts automatically and to have access to the web page (if it is located on a server).
If the Raspberry does not have internet access, the scripts can be downloaded manually onto the raspberry pi. The USB video loop still works without internet access.

Raspberry Pi Requirements:
- Raspberry Pi
- Micro SD
- Power Cable
- Monitor or TV
- Raspbian Stretch
- Internet connection (optional)


## Installation
To install the scripts, simply download the [installer.sh](https://gitlab.com/j.torrents/projecte/blob/master/Scripts/installer.sh) (located in the folder scripts)
and execute it on a freshly installed Raspbian in a Raspberry Pi.
Aditionally, all the scripts that need to be in the Raspberry are located on the [Scripts](https://gitlab.com/j.torrents/projecte/tree/master/Scripts) folder.

Note: The script must be executed with sudo!

That script will make the following changes:
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

After rebooting the system will be updated, upgraded and with all the scripts ready to be used. They will be located in:
```bash
/home/{new user}/scripts
```

Very Important!: Remember to change the IP, user and the password needed to make the connection to the data base in the finel code of the script.

Note: The zip containing the scripts must be named: scripts.zip. If not, some scripts will not work because after unzipping them a folder named scripts gets created
automatically. If you want to change the name of the folder keep in mind that some scripts will need to be modified because they all use the folloing path:
```bash
/home/{new user}/scripts/{script name}.sh
```


## Update all the scripts
If you want to update one or more scripts, simply zip them all (the modified ones and the old ones, basically every script located in the script folder) and put your .zip in GitHub. Then modify the installer.sh and look for this lines of code:
```bash
#Download all necessary scripts from GitLab
wget https://github.com/Shyrkoon/Base-de-dades/blob/master/proejcte/scripts.zip?raw=true
```
After the 'wget' command put your link of the raw zip of your GitHub. Then execute the script. The folder 'scripts' will be deleted and downloaded again.
Don't worry about all the changes that the script makes, if they have already been made, nothing will change on the Raspberry.
The script can be executed as many times as you want.

Alternatively the scripts can be edited in the Raspberry or simply, deleted and downloaded again.

## Usage
Aqui va una guia de como se usa todo lo que tenemos.
Abajo pondremos detalladamente todo lo que hace cada script i todo lo que puede hacer nuestra pagina web.

### Scripts

We have plenty of scripts to manage every thing in the Raspberry. 

All the documentation can be found in the [README](https://gitlab.com/j.torrents/projecte/tree/master/Scripts) in the scripts folder.

The most important script is ``` installer.sh ``` wich sets up the Raspberry and downloads all the other scripts.

### Web Page
The web page is responsible for managing this entire system, it is the bridge between the end user and the scripts.

We have the normal version to be implemented in a public web server, and the version for local web servers, **the only difference is that the local version does not implement Google's reCAPTCHA and uses local IPs instead of public ones.**

[Web Interface](https://gitlab.com/j.torrents/projecte/tree/master/Web%20Interface)

[Web Interface Local](https://gitlab.com/j.torrents/projecte/tree/master/Web%20Interface%20Local)

## Used scripts from GitHub
This project couldn't have been possible without the script that loops videos on a Raspberry Pi
made by [Tim Schwartz](https://github.com/timatron/videolooper-raspbian) which gave us the idea 
to improve it by making some more scripts to add features and to make a web page to control
everything that happens in the Raspberry.

All of this was made as a final project for the Spanish ASIX course.

Made by Jose and Raúl.

Special thanks to Orbis360. 
