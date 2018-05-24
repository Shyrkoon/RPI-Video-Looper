#!/bin/bash

#This is the script that downloads videos from youtube
#It also supports a variety of other streaming services

#Save the link into a variable
link=$1

#Location where the video will be saved
location=$2

#To be finished. This is the custom name of the folder
name=$3

#Download the video
youtube-dl --newline -o "$2/$3.%(ext)s" -f 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/bestvideo+bestaudio' --merge-output-format mp4 "$1" | grep --line-buffered -oP '^\[download\].*?\K([0-9.]+\%|#\d+ of \d)'
