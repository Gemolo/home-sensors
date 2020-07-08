from flask import Flask, request
import RPi.GPIO as GPIO
import time


app = Flask(__name__)

@app.route('/distance')
def distance():
    transmitter = int(request.args.get("transmitter"))
    receiver = int(request.args.get("receiver"))

    GPIO.setmode(GPIO.BCM)
    GPIO.setup(transmitter, GPIO.OUT)
    GPIO.setup(receiver, GPIO.IN)

    # set Trigger to HIGH
    GPIO.output(transmitter, True)
 
    # set Trigger after 0.01ms to LOW
    time.sleep(0.00001)
    GPIO.output(transmitter, False)
 
    StartTime = time.time()
    StopTime = time.time()
 
    # save StartTime
    while GPIO.input(receiver) == 0:
        StartTime = time.time()
 
    # save time of arrival
    while GPIO.input(receiver) == 1:
        StopTime = time.time()
 
    # time difference between start and arrival
    TimeElapsed = StopTime - StartTime
    # multiply with the sonic speed (34300 cm/s)
    # and divide by 2, because there and back
    distance = (TimeElapsed * 34300) / 2
 
    return str(int(distance)) + " cm"


def getPinValue(pin):
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(pin,  GPIO.IN)
    ret = GPIO.input(pin)
    GPIO.cleanup();
    return ret

@app.route('/light')
@app.route('/gas')
@app.route('/rain')
@app.route('/fire')
def zeroTrue():
    pin = int(request.args.get("gpio"))
    return str(getPinValue(pin) == 0)

@app.route('/movement')
def zeroFalse():
    pin = int(request.args.get("gpio"))
    return str(getPinValue(pin) != 0)


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)