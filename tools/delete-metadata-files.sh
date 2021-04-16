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

# unset all variables
unset PROFILE
unset VERSION
unset REGION
unset CATALOG
unset WORKGROUP
unset QUERY_OUTPUT
unset AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL1QUERIES
unset AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL1QUERIES
unset AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL2QUERIES
unset AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL2QUERIES
unset AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL3QUERIES
unset AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL3QUERIES
unset AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL4QUERIES
unset AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL4QUERIES
unset AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL5QUERIES
unset AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL5QUERIES
unset AWS_DEFAULT_SIMULTANEOUS_DDL_QUERIES
unset AWS_DEFAULT_SIMULTANEOUS_DML_QUERIES

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
