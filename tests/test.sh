#!/bin/bash

#
# CONFIGURATION
#

DIR=$( cd $( dirname "$0" ) > /dev/null 2>&1 && pwd )
source $DIR/configuration.sh

#
# DATABASE & TABLES
#

echo "The following critical operations are about to be performed:"
echo -e "\t- drop database $DATABASE"
echo -e "\t- delete data under s3://$OUTPUT_BUCKET/$DATABASE/"
echo -n "Do you agree? (Y/N): "
read confirm
if [[ $confirm != 'Y' ]] && [[ $confirm != 'y' ]]; then
    echo "Bye."
    exit
fi

# remove previous data, database, tables
echo "[ cleaning previous test data ]"
php ../tools/database.php \
    -d -t \
    -n $DATABASE \
> /dev/null && \
aws s3 --profile $AWS_PROFILE rm s3://$OUTPUT_BUCKET/$DATABASE/ --recursive

echo

# list sampledb database
echo "[ list sampledb database ]"
php ../tools/list-databases.php -n sampledb

if [[ $? -eq 1 ]]; then echo "Sampledb not present, exiting"; exit; fi

echo

# create database
echo "[ create database ]"
php ../tools/database.php \
    -c \
    -n $DATABASE

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# create data
echo "[ create data ]"
sed "s/_DATABASE_/$DATABASE/g;s/_OUTPUT_BUCKET_/$OUTPUT_BUCKET/g" create-data.sql \
| php ../tools/query-daily.php \
    -b $YYYY-$MM-01 \
    -e $YYYY-$MM-05 \
    -u 2 \
    -x DML

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# create table for created data
echo "[ create table for created data ]"
sed "s/_DATABASE_/$DATABASE/g;s/_OUTPUT_BUCKET_/$OUTPUT_BUCKET/g" create-table.sql \
| php ../tools/query-monthly.php \
    -b $YYYY-$MM \
    -x DDL

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# create day partitions
echo "[ create day partitions ]"
php ../tools/partitions-daily.php \
    -t $DATABASE.sampledb_$YYYY$MM \
    -d $OUTPUT_BUCKET/$DATABASE/sampledb/$YYYY/$MM \
    -y $YYYY \
    -m $MM \
    -e 05

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# single query
echo "[ query ]"
query="SELECT request_timestamp, elb_name, request_ip, request_port FROM $DATABASE.sampledb_$YYYY$MM LIMIT 5"
echo "$query"
id=$(echo "$query" \
| php ../tools/query.php \
    -o $OUTPUT_BUCKET/$DATABASE/query \
    -s \
) && \
echo && \
if [[ $? -eq 0 ]]; then
    # get query state
    echo "[ get query state ]"
    php ../tools/state.php -i "$id"

    echo

    # stop query
    echo "[ stop query ]"
    php ../tools/stop.php -i "$id"

    echo

    # display query results
    echo "[ display query results ]"
    aws s3 --profile $AWS_PROFILE cp s3://$OUTPUT_BUCKET/$DATABASE/query/$id.csv -
else
    echo "Command failed, exiting"
    exit
fi

echo

# query daily
echo "[ query daily ]"
query="SELECT * FROM $DATABASE.sampledb_%1\$s%2\$s WHERE day = '%3\$s'"
echo "$query"
echo "$query" \
| php ../tools/query-daily.php \
    -b $YYYY-$MM-02 \
    -e $YYYY-$MM-03 \
    -x DML \
    -o $OUTPUT_BUCKET/$DATABASE/query-daily

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# display query results on s3
echo "[ display query results on s3 ]"
aws s3 --profile $AWS_PROFILE ls s3://$OUTPUT_BUCKET/$DATABASE/query-daily/ --recursive

echo

# delete metadata files
echo "[ delete metadata files ]"
/bin/bash ../tools/delete-metadata-files.sh \
    -b $OUTPUT_BUCKET \
    -f $DATABASE/query-daily

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# display query results on s3
echo "[ display query results on s3 without metadata files ]"
aws s3 --profile $AWS_PROFILE ls s3://$OUTPUT_BUCKET/$DATABASE/query-daily/ --recursive

echo

# list tables
echo "[ list database table like '*db_"$YYYY$MM" ]"
php ../tools/list-tables.php \
    -n $DATABASE \
    -t '*db_'$YYYY$MM

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# drop database + all tables
echo "[ drop database + tables ]"
php ../tools/database.php \
    -d -t \
    -n $DATABASE

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# delete data from s3
echo "[ delete created data from s3 ]"
aws s3 --profile $AWS_PROFILE rm s3://$OUTPUT_BUCKET/$DATABASE/ --recursive

echo

#
# NAMED QUERY
#

# create named query
echo "[ create named query ]"
query="SELECT * FROM elb_logs ORDER BY request_timestamp DESC LIMIT 352"
echo "$query"
id=$(echo "$query" \
| php ../tools/named-query.php \
    -s -c \
    -n sampledb \
    -j 'log sample' \
    -z 'get the latest 352 log records' \
) && echo "$id"

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# list named queries
echo "[ list that named query ]"
php ../tools/list-named-queries.php -i "$id"

if [[ $? -eq 1 ]]; then echo "Command failed, exiting"; exit; fi

echo

# delete named query
echo "[ delete named query ]"
php ../tools/named-query.php -d -i "$id"

echo

echo "Done."

