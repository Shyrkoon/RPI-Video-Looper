#!/bin/bash

#This script removes one video
#It needs to get the name and location of the video

#Video Location
location=$1

#Video name
name=$2

#Remove the video
rm -f "$1""$2"

echo "$2 has been deleted from $1"
