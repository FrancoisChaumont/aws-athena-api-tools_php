#!/bin/bash

# read .env file
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

AWS_PROFILE=$PROFILE
OUTPUT_BUCKET=$QUERY_OUTPUT

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

DATABASE=''
YYYY=''
MM=''

if [[ "$1" == "" ]]; then echo "Missing parameters!"; exit; fi

while [ -n "$1" ]; do
    case "$1" in 
        -d | --database)            DATABASE="$2"; shift ;;
        -y | --year)                YYYY="$2"; shift ;;
        -m | --month)               MM="$2"; shift ;;

        *) ;;
    esac
    shift
done

if [[ $DATABASE == '' ]]; then echo "Missing parameter DATABASE!"; exit; fi
if [[ $YYYY == '' ]]; then echo "Missing parameter YEAR!"; exit; fi
if [[ $MM == '' ]]; then echo "Missing parameter MONTH!"; exit; fi
