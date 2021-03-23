CREATE EXTERNAL TABLE _DATABASE_.sampledb_%1$s%2$s (
	request_timestamp string, 
	elb_name string, 
	request_ip string, 
	request_port int, 
	backend_ip string, 
	backend_port int, 
	request_processing_time double, 
	backend_processing_time double, 
	client_response_time double, 
	elb_response_code string, 
	backend_response_code string, 
	received_bytes bigint, 
	sent_bytes bigint, 
	request_verb string, 
	url string, 
	protocol string, 
	user_agent string, 
	ssl_cipher string, 
	ssl_protocol string
)
PARTITIONED BY (day string)
ROW FORMAT SERDE 'org.apache.hadoop.hive.ql.io.parquet.serde.ParquetHiveSerDe'
WITH SERDEPROPERTIES (
  'serialization.format' = '1'
) LOCATION 's3://_OUTPUT_BUCKET_/_DATABASE_/sampledb/'
