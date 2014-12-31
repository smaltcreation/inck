#!/bin/sh

id=`ps aux | grep '[p]hp -f app/console inck:ratchet:start' | awk '{print $2}'`

if [ ! -z "$id" ]; then
    echo "$(tput setaf 2)Ratchet is on : pid = ${id}$(tput sgr0)"
else
    echo "$(tput setaf 1)Ratchet is off !$(tput sgr0)"
fi
