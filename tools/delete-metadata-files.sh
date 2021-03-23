#!/bin/bash

# Delete metadata files recursively from an S3 location (bucket/prefixes)

DIR=$( cd $( dirname "$0" ) > /dev/null 2>&1 && pwd )
source $DIR/usage/delete-metadata-files.usage.sh

if [[ ! -f $DIR/../.env.dev ]]; then
    if [[ ! -f $DIR/../.env ]]; then
        echo -e "Missing configuration variable file!"
        exit 1
    else
        ENV="$DIR/../.env"
    fi
else
    ENV="$DIR/../.env.dev"
fi

# retrieve configuration variables
set -o allexport
source $ENV
set +o allexport

profile=$PROFILE

if [[ "$1" == "" ]]; then display_usage; exit; fi

while [ -n "$1" ]; do
    case "$1" in 
        -h | --help)    display_usage; exit ;;

        -b | --bucket)  bucket="$2"; shift ;;
        -f | --prefix)  prefix="$2"; shift ;;
        -p | --profile) profile="$2"; shift ;;

        *) ;;
    esac
    shift
done

while read -r file; do
    aws s3 --profile $profile rm s3://$bucket/$file;
done <<< $(aws s3 --profile $profile ls s3://$bucket/$prefix/ --recursive | grep '.metadata$' | awk '{print $4}')
