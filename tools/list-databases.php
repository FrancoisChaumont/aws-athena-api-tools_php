<?php

/**
 * Detail one or all databases and output to json format
 */

namespace FC\AWS;

require __DIR__ . "/imports/common.php";
require __DIR__ . "/usage/list-databases.usage.php";

// type of query/ies executed 
const QUERY_TYPE = \FC\AWS\Athena::QUERY_TYPE_DML;

try {
    // init configuration parameters
    if (!isset($options[OPTION_CATALOG])) { $catalog = DEFAULT_CATALOG; }
    else { $catalog = $options[OPTION_CATALOG]; }

    // AWS Athena client configuration
    $awsConfig = getAwsConfig($options);

    // init Athena object
    $athena = instantiateAthena(new \Aws\Athena\AthenaClient($awsConfig));

    // detail one or all databases
    if (isset($options[OPTION_DATABASE])) {
        echo json_encode($athena->getDatabaseDetails($options[OPTION_DATABASE], $catalog)) . PHP_EOL;
    } else {
        foreach($athena->listAllDatabases($catalog) as $databaseName) {
            echo json_encode($athena->getDatabaseDetails($databaseName, $catalog)) . PHP_EOL;
        }
    }

    exit(0);

} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
