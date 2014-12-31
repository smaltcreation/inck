#!/bin/sh

echo "Starting Ratchet..."
php -f app/console inck:ratchet:start > app/logs/ratchet.output 2>&1 &
