#!/bin/bash

# Usage details for the delete-metadata-files tool

function display_usage()
{
    included_file="delete-metadata-files.sh"
    whatItDoes="Delete metadata files recursively from an S3 location (bucket/prefixes)"

    cat << EOS
What it does:

    $whatItDoes

Exit code:
    0 - success
    1 - failure

Usage: $included_file [options]

Options:
    -h/--help                               Show this help message and exit

    -b/--bucket=bucket                      [REQUIRED] S3 bucket (no 's3://', no trailing '/')
    -f/--prefix=prefixes                    [REQUIRED] S3 bucket prefix(es) (no leading or trailing '/')
    -p/--profile=aws_profile                [OPTIONAL] AWS profile to use credentials from
                                                       Default value: PROFILE in configuration variable file

    $included_file -b BUCKET -f PREFIXES [-p AWS_PROFILE]
    $included_file --bucket BUCKET --prefix PREFIXES [--profile AWS_PROFILE]

Example:
    $included_file \\
        -b bucket
        -f prefixes
        -p default
EOS
}
