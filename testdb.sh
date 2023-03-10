#!/bin/bash

CONTAINER_ID=$(docker ps --filter "name=step_mysql_1" --format "{{.ID}}")

# -pxxxxx => password付きで接続
# -itはつけない
# shell script は tty対応してないし、interface も不要なため
docker exec -i $CONTAINER_ID mysql -u root -proot << EOF
CREATE DATABASE IF NOT EXISTS db_testing;

GRANT ALL ON db_testing.* TO user;

exit
EOF

echo 'done'
