#!/bin/sh
while [[ true ]]
do
curl http://home-sensors-dev_nginx_1/api/check
sleep 0.5
done