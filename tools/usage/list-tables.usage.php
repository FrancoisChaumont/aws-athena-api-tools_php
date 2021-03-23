<?php

// retrieve the name of the file that includes this usage file
define('INCLUDED_FILE', basename(get_included_files()[0]));

// modify the description here below
$whatItDoes = <<<EOS
Detail one or all tables of a database and output to json format

Exit code:
    0 - databases listed successfully
    1 - databases list execution failed
EOS;

define('USAGE', sprintf(USAGE_INTRO, $whatItDoes, INCLUDED_FILE, sprintf(<<<EOS
[options]

Options:
    -h/--help                               Show this help message and exit

    -n/--database=database_name             [REQUIRED] Database name
    -t/--table=table_name                   [OPTIONAL] Table name or regular expression
    -k/--catalog=catalog_name               [OPTIONAL] Athena data source catalog
                                                       Default value: CATALOG in configuration variable file
    -p/--profile=aws_profile                [OPTIONAL] AWS profile to use credentials from
                                                       Default value: PROFILE in configuration variable file
    -v/--version=aws_version                [OPTIONAL] Version of the AWS webservice to utilize
                                                       Default value: VERSION in configuration variable file
    -r/--region=aws_region                  [OPTIONAL] AWS region to connect to
                                                       Default value: REGION in configuration variable file

    php %1\$s -n DATABASE_NAME [-t TABLE_NAME] [-p AWS_PROFILE] [-v AWS_VERSION] [-r AWS_REGION]
    php %1\$s --database DATABASE_NAME [--table TABLE_NAME] [--profile AWS_PROFILE] [--version AWS_VERSION] [--region AWS_REGION]

Example:
    php %1\$s \
        -n database_name \
        -k AwsDataCatalog \
        -p default \
        -v latest \
        -r us-east-1

    php %1\$s \
        -n database_name \
        -t '.*part_of_table_name.*' \
        -k AwsDataCatalog \
        -p default \
        -v latest \
        -r us-east-1
EOS, INCLUDED_FILE)));

$shortOpts = 'n:t:k:p:v:r:h';
$longOpts = [
    'database:',
    'table:',
    'catalog:',
    'profile:',
    'version:',
    'region:',
    'help'
];

// set options
$options = setOptions($shortOpts, $longOpts, USAGE);

define('OPTION_DATABASE', isset($options['n']) ? 'n' : 'database');
define('OPTION_TABLE', isset($options['t']) ? 't' : 'table');
define('OPTION_CATALOG', isset($options['k']) ? 'k' : 'catalog');

// Initialize AWS configuration options
initAwsConfigOptions($options);
