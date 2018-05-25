#!/bin/bash
usuariSSH=$1
passwordSSH=$2
ipRPI=$3

sshpass -p "$passwordSSH" ssh -o ConnectTimeout=15 StrictHostKeyChecking=no "$usuariSSH"@"$ipRPI" &> /dev/null exit
test=$?
if [ $test -eq 0 ]
then
	echo '<button id="statusButton" type="button" class="btn btn-success w3-animate-zoom">Online</button>'
else
	echo '<button id="statusButton" type="button" class="btn btn-danger w3-animate-zoom">Offline</button>'
fi

############################OLD################################
#status=$(sudo ssh -o Batchmode=yes -o ConnectTimeout=5 -oStrictHostKeyChecking=no -i /home/ubuntu/Projecte.pem ubuntu@172.31.22.120 echo ok 2>&1)
#if [[ $status == ok ]] ; then
#  echo Online
#elif [[ $status == "Permission denied"* ]] ; then
#  echo no_auth
#else
#  echo Offline
#fi
