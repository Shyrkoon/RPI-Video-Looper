#!/bin/bash
read mac < <(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/address)
read publicIP < <(curl -s http://whatismyip.akamai.com/)
while true
do
read newPublicIP < <(curl -s http://whatismyip.akamai.com/)
echo $mac
echo $newPublicIP
if [ $newPublicIP != $publicIP ]; then
sudo mysql -h 34.217.243.108 -u rpiClient -prpiClients1 projecteRPI --ssl-key=~/Projecte.pem <<EOF
UPDATE raspberries
SET ipPublica  = "$newPublicIP"
WHERE mac="$mac";
EOF
fi
sleep 60
done
