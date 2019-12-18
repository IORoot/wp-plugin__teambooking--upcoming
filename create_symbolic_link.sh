#!/bin/bash

# CREATE A SYMBOLIC LINK FOR INPUT


# Take two arguments.
if [ "$#" -ne 2 ]; then
  echo "Usage: $0 OLDFILE NEWFILE" >&2
  exit 1
fi

OLDPATH=$1
NEWPATH=$2

# Check old file exists
if [ ! -f $OLDPATH ]
then
    echo "The source ${OLDPATH} does not exist. Aborting"
    exit
fi

# If the oldfile is already a link, abort.
if [ -L $OLDPATH ]
then
    echo "${OLDPATH} is already a link. Aborting."
    exit
fi

# Backup existing file.
mv ${OLDPATH} ${OLDPATH}.bak

# Create symbolic Link
ln -s $NEWPATH $OLDPATH

echo done.