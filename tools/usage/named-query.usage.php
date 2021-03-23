<?php

// retrieve the name of the file that includes this usage file
define('INCLUDED_FILE', basename(get_included_files()[0]));

// modify the description here below
$whatItDoes = <<<EOS
Create/Delete a named query

Exit code:
    0 - named query created/deleted successfully
    1 - named query creation/deletion failed
EOS;

define('USAGE', sprintf(USAGE_INTRO, $whatItDoes, INCLUDED_FILE, sprintf(<<<EOS
[options]

Options:
    -h/--help                               Show this help message and exit

    -c/--create                             [REQUIRED] Create a named query¹
    -d/--delete                             [REQUIRED] Delete a a named query¹
    -q/--script=path_to_script              [OPTIONAL] Path to query script²
                                                       Reads from STDIN when omitted
    -i/--id=query_id                        [REQUIRED] Named query ID³
    -s/--silent                             [OPTIONAL] Only displays the created query ID
    -n/--database=database_name             [REQUIRED] Name of the database²
    -j/--name=query_name                    [REQUIRED] Name of the query²
    -z/--description=query_description      [REQUIRED] Description for the query²
    -p/--profile=aws_profile                [OPTIONAL] AWS profile to use credentials from
                                                       Default value: default
    -v/--version=aws_version                [OPTIONAL] Version of the AWS webservice to utilize
                                                       Default value: latest
    -r/--region=aws_region                  [OPTIONAL] AWS region to connect to
                                                       Default value: us-east-1
    -w/--display                            [OPTIONAL] Only displays query for syntax verification (only for option -c)

    /!\ ¹Either -c/--create or -d/--delete
    /!\ ²Option -c/--create requires option -q/--script, -b/--database, -m/--queryname, [-z/--querydescription]
    /!\ ³Option -d/--delete requires option -i/--id

    php %1\$s -c -q PATH_TO_SCRIPT -n DATABASE_NAME -j QUERY_NAME -z QUERY_DESCRIPTION [-p AWS_PROFILE] [-v AWS_VERSION] [-r AWS_REGION] [-w] [-s]
    php %1\$s --create --script PATH_TO_SCRIPT --database DATABASE_NAME --name QUERY_NAME --description QUERY_DESCRIPTION [--profile AWS_PROFILE] [--version AWS_VERSION] [--region AWS_REGION] [--display] [--silent]
    php %1\$s -d -i QUERY_ID [-p AWS_PROFILE] [-v AWS_VERSION] [-r AWS_REGION]
    php %1\$s --delete --id QUERY_ID [--profile AWS_PROFILE] [--version AWS_VERSION] [--region AWS_REGION]

Example:
    echo "SELECT * FROM sampledb.elb_logs ORDER BY request_timestamp DESC LIMIT 352" \
    | php %1\$s \
        -c \
        -n sampledb \
        -j 'log sample'

    php %1\$s \
        -c \
        -q ../../examples/named-query.sql \
        -n sampledb \
        -j 'log sample' \
        -z 'get the latest 352 log records' \
        -p default \
        -v latest \
        -r us-east-1

    php %1\$s \
        -d \
        -i d9d3c918-7d3d-454d-a9af-e2721ecf0bf6 \
        -p default \
        -v latest \
        -r us-east-1
EOS, INCLUDED_FILE)));

$shortOpts = 'cdi:q:n:j:z:p:v:r:swh';
$longOpts = [
    'create',
    'delete',
    'id:',
    'script:',
    'database:',
    'name:',
    'description:',
    'profile:',
    'version:',
    'region:',
    'silent',
    'display',
    'help'
];

// set options
$options = setOptions($shortOpts, $longOpts, USAGE);

define('OPTION_CREATE', isset($options['c']) ? 'c' : 'create');
define('OPTION_SCRIPT', isset($options['q']) ? 'q' : 'script');
define('OPTION_DATABASE', isset($options['n']) ? 'n' : 'database');
define('OPTION_NAME', isset($options['j']) ? 'j' : 'name');
define('OPTION_DESCRIPTION', isset($options['z']) ? 'z' : 'description');
define('OPTION_DELETE', isset($options['d']) ? 'd' : 'delete');
define('OPTION_ID', isset($options['i']) ? 'i' : 'id');
define('OPTION_DISPLAY', isset($options['w']) ? 'w' : 'display');
define('OPTION_SILENT', isset($options['s']) ? 's' : 'silent');

// Initialize AWS configuration options
initAwsConfigOptions($options);
