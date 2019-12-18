#!/bin/bash

# REMOVE A SYMBOLIC LINK AND RESTORE BACKUP

# Take one arguments.
if [ "$#" -ne 1 ]; then
  echo "Usage: $0 OLDFILE" >&2
  exit 1
fi

OLDPATH=$1

# If the oldfile is not a link, abort.
if [ ! -L $OLDPATH ]
then
    echo "${OLDPATH} is not a link. Aborting."
    exit
fi

# Check old file exists
if [ ! -f $OLDPATH.bak ]
then
    echo "The backup file ${OLDPATH}.bak does not exist. Aborting"
    exit
fi

# Remove the link
rm ${OLDPATH}

# Backup existing file.
mv ${OLDPATH}.bak ${OLDPATH}

echo done.