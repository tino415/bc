#!/bin/bash
HOST="yweb.sk"
USER="bcmusic"
PASS="?zsgd#1"
TARGETFOLDER='/new'
SOURCEFOLDER='/home/martin/Projects/bc'

lftp -f "
set ftp:ssl-allow no
open $HOST
user $USER $PASS
lcd $SOURCEFOLDER
mirror --reverse --delete --verbose $SOURCEFOLDER $TARGETFOLDER
bye
"
