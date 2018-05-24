#!/bin/bash
read mac < <(cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/address)
IP=$(echo "SELECT ipPublica FROM raspberries WHERE mac='$mac'" | mysql -N -h 34.217.243.108 projecteRPI --ssl-key=~/Projecte.pemmysql -h 34.217.243.108 -u rpiClient -prpiClients1 --ssl-key=~/Projecte.pem)

while true
do
read newIP < <(ifconfig | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
echo $mac
echo $newIP
if [ "$newIP" != "$IP" ]; then
sudo mysql -h 34.217.243.108 -u rpiClient -prpiClients1 projecteRPI --ssl-key=~/Projecte.pem <<EOF
UPDATE raspberries
SET ipPublica  = "$newIP"
WHERE mac="$mac";
EOF
IP=$(echo "SELECT ipPublica FROM raspberries WHERE mac='$mac'" | mysql -N -h 34.217.243.108 projecteRPI --ssl-key=~/Projecte.pemmysql -h 34.217.243.108 -u rpiClient -prpiClients1 --ssl-key=~/Projecte.pem)
fi
sleep 60
done
