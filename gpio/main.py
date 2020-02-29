from flask import Flask
import RPi.GPIO as GPIO
import time


app = Flask(__name__)

def distance():
    # set Trigger to HIGH
    GPIO.output(23, True)
 
    # set Trigger after 0.01ms to LOW
    time.sleep(0.00001)
    GPIO.output(23, False)
 
    StartTime = time.time()
    StopTime = time.time()
 
    # save StartTime
    while GPIO.input(24) == 0:
        StartTime = time.time()
 
    # save time of arrival
    while GPIO.input(24) == 1:
        StopTime = time.time()
 
    # time difference between start and arrival
    TimeElapsed = StopTime - StartTime
    # multiply with the sonic speed (34300 cm/s)
    # and divide by 2, because there and back
    distance = (TimeElapsed * 34300) / 2
 
    return distance

@app.route('/')
def index():
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(13,  GPIO.IN)
    GPIO.setup(16,  GPIO.IN)
    GPIO.setup(19,  GPIO.IN)
    GPIO.setup(20,  GPIO.IN)
    GPIO.setup(21,  GPIO.IN)
    GPIO.setup(23,  GPIO.OUT)
    GPIO.setup(24,  GPIO.IN)
    GPIO.setup(26,  GPIO.IN)

    ret = {
        'luce': GPIO.input(13) == 0,
        'gas': GPIO.input(16) == 0,
        'pir0': GPIO.input(19) == 1,
        'pioggia': GPIO.input(20) == 0,
        'fuoco': GPIO.input(21) == 0,
        'tracking': GPIO.input(26) == 0,
        'metro': distance()
    }
    GPIO.cleanup()
    return ret



if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)