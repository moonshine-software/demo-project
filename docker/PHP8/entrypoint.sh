#!/bin/ash

#!/bin/sh - if you get the error entrypoint.sh not found, delete first line #!/bin/ash

set -e;

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf;

php-fpm;
