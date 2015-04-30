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
mirror --exclude commands/ --exclude composer.josn --exclude composer.lock --exclude doc/ --exclude ftp-sync.sh --exclude lfm.log --exclude LICENSE.md --exclude migrations/ --exclude README.md --exclude requirements.php --exclude runtime/ --exclude sql/ --exclude stopwords/ --exclude tags --exclude tags.sh --exclude yii --exclude yii.bat --exclude .git/ --reverse --delete --verbose $SOURCEFOLDER $TARGETFOLDER
bye
"
