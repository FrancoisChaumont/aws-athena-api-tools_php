CREATE TABLE _DATABASE_.sampledb_%1$s%2$s%3$s
WITH (
    external_location = 's3://_OUTPUT_BUCKET_/_DATABASE_/sampledb/%1$s/%2$s/%3$s',
    format = 'PARQUET',
    parquet_compression = 'SNAPPY'
) AS
SELECT *
FROM sampledb.elb_logs 
WHERE DATE_FORMAT(FROM_ISO8601_TIMESTAMP(request_timestamp), '%%Y-%%m-%%d') = '%1$s-%2$s-%3$s'
LIMIT 100