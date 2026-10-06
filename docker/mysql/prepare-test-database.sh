#!/bin/sh

set -eu

case "${TEST_DB_NAME:-}" in
    "${MYSQL_DATABASE:-}"|''|*[!a-zA-Z0-9_]*)
        echo 'TEST_DB_NAME must be a safe name different from MYSQL_DATABASE.' >&2
        exit 1
        ;;
    *_test) ;;
    *)
        echo 'TEST_DB_NAME must end with "_test".' >&2
        exit 1
        ;;
esac

case "${MYSQL_USER:-}" in
    ''|*[!a-zA-Z0-9_]*)
        echo 'MYSQL_USER must contain only letters, numbers, and underscores.' >&2
        exit 1
        ;;
esac

MYSQL_PWD="${MYSQL_ROOT_PASSWORD}" mysql --protocol=socket --user=root <<SQL
CREATE DATABASE IF NOT EXISTS \`${TEST_DB_NAME}\`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${TEST_DB_NAME}\`.* TO '${MYSQL_USER}'@'%';
SQL

echo "Test database \"${TEST_DB_NAME}\" is ready."
