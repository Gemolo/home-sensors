FROM python:3
USER root

WORKDIR /usr/src/app

RUN pip install RPi.GPIO
RUN pip install flask

COPY . .


CMD [ "python", "./main.py" ]
