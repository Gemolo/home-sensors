#!/bin/sh
while [[ true ]]
do
result="$(curl http://home-sensors-webserver/api/check >/dev/null)"
echo ${result}
sleep 5
done