#!/bin/sh
while [[ true ]]
do
curl http://home-sensors-webserver/api/check
sleep 1
done