#!/bin/bash
if [ $3 = 'SI' ]; then
   cd /public_html/videos
   mkdir $1
   cd $1
   youtube-dl -x --audio-format mp3 $2
   
else
    if [ $4 = 0 ]; then
       cd /public_html/videos
       mkdir $1
       cd $1
       youtube-dl $2
    else
       cd /public_html/videos
       mkdir $1
       cd $1
       youtube-dl $2 -f $4
    fi
fi
