<?php

/**
 * Common to all tools
 */

ini_set("display_errors", true);
ini_set("error_reporting", E_ALL);

require __DIR__ . "/../../vendor/autoload.php";

// query ID format
const ID_FORMAT = '[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}';

// output location for query results
const QUERY_OUTPUT_LOCATION = "s3://%s/";

// introduction to usage details
const USAGE_INTRO = "What it does:\n\n    %s\n\nUsage: php %s %s\n";

$envFilePath = __DIR__ . "/../..";
$envFileProd = '.env';
$envFileDev = '.env.dev';
$envFile = is_readable("$envFilePath/$envFileDev") ? $envFileDev : (is_readable("$envFilePath/$envFileProd") ? $envFileProd : null);

try {
    if (is_null($envFile)) {
        throw new \Exception("Configuration variable file not found or not readable! Expecting a .env file under directory $envFilePath.");
    }

    if (sizeof(\Dotenv\Dotenv::createImmutable($envFilePath, $envFile)->load()) == 0) {
        throw new \Exception("Make sure they were not previously loaded.");
    }

    define('DEFAULT_PROFILE', $_ENV['PROFILE']);
    define('DEFAULT_VERSION', $_ENV['VERSION']);
    define('DEFAULT_REGION', $_ENV['REGION']);
    define('DEFAULT_CATALOG', $_ENV['CATALOG']);
    define('DEFAULT_WORKGROUP', $_ENV['WORKGROUP']);
    define('DEFAULT_QUERY_OUTPUT', $_ENV['QUERY_OUTPUT']);

    define('DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL1QUERIES', $_ENV['AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL1QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL1QUERIES', $_ENV['AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL1QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL2QUERIES', $_ENV['AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL2QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL2QUERIES', $_ENV['AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL2QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL3QUERIES', $_ENV['AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL3QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL3QUERIES', $_ENV['AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL3QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL4QUERIES', $_ENV['AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL4QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL4QUERIES', $_ENV['AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL4QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL5QUERIES', $_ENV['AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL5QUERIES']);
    define('DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL5QUERIES', $_ENV['AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL5QUERIES']);
    define('DEFAULT_AWS_DEFAULT_SIMULTANEOUS_DDL_QUERIES', $_ENV['AWS_DEFAULT_SIMULTANEOUS_DDL_QUERIES']);
    define('DEFAULT_AWS_DEFAULT_SIMULTANEOUS_DML_QUERIES', $_ENV['AWS_DEFAULT_SIMULTANEOUS_DML_QUERIES']);

} catch (\Exception $e) {
    echo "Failed to load configuration variables from file: " . $e->getMessage();
    exit(1);
}

/**
 * Set options and display usage
 *
 * @param string $shortOpts list of short options
 * @param array $longOpts array of long options
 * @param string $usage usage details to display
 * @return array options
 */
function setOptions(string $shortOpts, array $longOpts, string $usage): array
{
    $options = getopt($shortOpts, $longOpts);
    if (isset($options['h']) || isset($options['help'])) {
        print $usage;
        exit;
    }

    return $options;
}

/**
 * Initialize AWS configuration options
 *
 * @param array $options
 * @return void
 */
function initAwsConfigOptions(array $options): void
{
    define('OPTION_PROFILE', isset($options['p']) ? 'p' : 'profile');
    define('OPTION_VERSION', isset($options['v']) ? 'v' : 'version');
    define('OPTION_REGION', isset($options['r']) ? 'r' : 'region');
}

/**
 * Initialize AWS configuration
 *
 * @param array $options
 * @return array associative array with profile, version and region initialized according to provided options
 */
function getAwsConfig(array $options): array
{
    return [
        'profile' => (isset($options[OPTION_PROFILE]) && $options[OPTION_PROFILE] != '' ? $options[OPTION_PROFILE] : DEFAULT_PROFILE),
        'version' => (isset($options[OPTION_VERSION]) && $options[OPTION_VERSION] != '' ? $options[OPTION_VERSION] : DEFAULT_VERSION),
        'region' => (isset($options[OPTION_REGION]) && $options[OPTION_REGION] != '' ? $options[OPTION_REGION] : DEFAULT_REGION)
    ];
}

function instantiateAthena(\Aws\Athena\AthenaClient $athenaClient, int $maxDDL = null, int $maxDML = null): \FC\AWS\Athena
{
    return new \FC\AWS\Athena(
        $athenaClient,
        DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL1QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL1QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL2QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL2QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL3QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL3QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL4QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL4QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_CALLS_PER_SECOND_LEVEL5QUERIES,
        DEFAULT_AWS_DEFAULT_MAX_BURST_CAPACITY_LEVEL5QUERIES,
        is_null($maxDDL) ? DEFAULT_AWS_DEFAULT_SIMULTANEOUS_DDL_QUERIES : $maxDDL,
        is_null($maxDML) ? DEFAULT_AWS_DEFAULT_SIMULTANEOUS_DML_QUERIES : $maxDML
    );
}

/**
 * Date&Time validator for all formats
 *
 * @param $datetime date&time, date, time (full or partial), can be integer or string
 * @param string $format date/time format
 * @return bool true if validated, false otherwise
 */
function validateDateTime($datetime, string $format = 'Y-m-d H:i:s'): bool
{
    $d = \DateTime::createFromFormat($format, $datetime);
    
    return $d && $d->format($format) == $datetime;
}

