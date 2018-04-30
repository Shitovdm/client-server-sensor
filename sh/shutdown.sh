#! /bin/bash

# ===========================================================================
# Shut Down or Reset button for Raspberry Pi B+ (40 GPIO pins)              #
#                                                                           #
# Used pins marked with star (*):                                           #
#                                                              *  *         #
#        2  4  6  8  10 12 14 16 18 20 22 24 26 28 30 32 34 36 38 40        #
#        |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |         #
#        ----------------------------------------------------------         #
#        |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |         #
#        1  3  5  7  9  11 13 15 17 19 21 23 25 27 29 31 33 35 37 39        #
#                                                                           #
#                                                                           #
# ===========================================================================

# Set up GPIO20 and set to output
echo "20" > /sys/class/gpio/export
echo "out" > /sys/class/gpio/gpio20/direction
echo "1" > /sys/class/gpio/gpio20/value

# Set up GPIO21 and set to input
echo "21" > /sys/class/gpio/export
echo "in" > /sys/class/gpio/gpio21/direction

while ( true ) 
do
    # check if the pin is connected to GND and, if so, halt the system
    if [ $(</sys/class/gpio/gpio21/value) == 1 ]
    then
        echo "20" > /sys/class/gpio/unexport
        echo "21" > /sys/class/gpio/unexport
        shutdown -h now "System halted by a GPIO action"
    fi 
    sleep 1
done