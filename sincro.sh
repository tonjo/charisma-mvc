#!/bin/bash

#HOST="ipcam"
#DIR="/var/www/ipcam"
#DESTINATIONS="$HOST:$DIR"
DESTINATIONS="/tmp/b"

# Setta EXCLUDE_numero e controlla che ci sia nella riga di comando rsync
EXCLUDE_1="*.db"

if [ -z "$DESTINATIONS" ]
  then
    echo "Specify DESTINATIONS"
    exit 2
fi

echo
echo " ===== ESCLUDO $EXCLUDE_1 $EXCLUDE_2 $EXCLUDE_3 $EXCLUDE_4"
echo

OPT=$1
SCRIPTNAME=$(basename $0)
LOG=./$SCRIPTNAME.log
sudo touch $LOG
sudo chown $USER:$USER $LOG

for DEST in $DESTINATIONS
do
  rsync -e "ssh -o ClearAllForwardings=yes"  --exclude=$EXCLUDE_1 --exclude=$EXCLUDE_2 -rlptDv . $DEST
done

ssh $HOST "chown -R www-data:www-data $DIR"
echo
echo " ========== SETTATI PERMESSI $HOST:$DIR, CONTROLLA !!! =========="
echo
