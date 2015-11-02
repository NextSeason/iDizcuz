#! /bin/sh

USER="next"
SERVER="123.56.129.236"
ROOTPATH="/home/idizcuz/www"

FILELIST=(
    "static_files/*"
    "application"
    "conf"
    "plugins"
    "public"
    "resources"
    "static"
)

for FILE in ${FILELIST[@]}; do
    scp -r $FILE $USER@$SERVER:$ROOTPATH
done

exit;
