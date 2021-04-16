## The tools
**Create/Drop a database**        
See [usage](../tools/usage/database.usage.php) or
```
php database.php -h/--help
```

**Execute a single query**      
select | create table [as] | create view | create database | delete table ...

See [usage](../tools/usage/query.usage.php) or
```
php query.php -h/--help
```

**Execute queries for each day in the given date range within the max rate limit**      
select | create table [as] | create view | create database | delete table ...

Examples: [query-daily.sql](../examples/query-daily.sql)       

See [usage](../tools/usage/query-daily.usage.php) or
```
php query-daily.php -h/--help
```

**Execute queries for each month in the given date range within the max rate limit**     
select | create table [as] | create view | create database | delete table ...

Examples: 
[create-table.sql](../examples/create-table.sql), 
[create-table-partitioned.sql](../examples/create-table-partitioned.sql), 
[create-table-partitioned-hive.sql](../examples/create-table-partitioned-hive.sql), 
[drop-table.sql](../examples/drop-table.sql), 
[query-monthly.sql](../examples/query-monthly.sql)

See [usage](../tools/usage/query-monthly.usage.php) or
```
php query-monthly.php -h/--help
```

**Create day partitions on a table (non-Hive formatted data)**       
See [usage](../tools/usage/partitions-daily.usage.php) or
```
php partitions-daily.php -h/--help
```

**Create day partitions on a table (Hive formatted data)**       
See [usage](../tools/usage/partitions-daily-hive.usage.php) or
```
php partitions-daily-hive.php -h/--help
```

**Get the execution state of a query (running, failed, succeeded, ...)**     
See [usage](../tools/usage/state.usage.php) or
```
php state.php -h/--help
```

**Get the execution state of queries listed in a file (running, failed, succeeded, ...)**     
Example: [query-ids-list.txt](../examples/query-ids-list.txt)

See [usage](../tools/usage/state-from-list.usage.php) or
```
php state-from-list.php -h/--help
```

**Stop a running query**        
See [usage](../tools/usage/stop.usage.php) or
```
php stop.php -h/--help
```

**Create or delete a named query**      
See [usage](../tools/usage/named-query.usage.php) or
```
php named-query.php -h/--help
```

**Detail one or all named queries and output to json format**     
See [usage](../tools/usage/list-named-queries.usage.php) or
```
php list-named-queries.php -h/--help
```

**Detail one or all databases and output to json format**     
See [usage](../tools/usage/list-databases.usage.php) or
```
php list-databases.php -h/--help
```

**Detail one or all tables of a database and output to json format**     
See [usage](../tools/usage/list-tables.usage.php) or
```
php list-tables.php -h/--help
```
