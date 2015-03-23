#!/bin/sh

echo "Stopping Ratchet..."
id=`ps aux | grep '[p]hp -f app/console inck:ratchet:start' | awk '{print $2}'`

if [ ! -z "$id" ]; then
    echo "$(tput setaf 4)kill ${id}$(tput sgr0)"
    sudo kill ${id}
else
    echo "$(tput setaf 4)Ratchet is already stopped$(tput sgr0)"
fi
