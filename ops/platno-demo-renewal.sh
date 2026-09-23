#!/bin/sh
set -eu

if [ "${RENEWED_LINEAGE:-}" != /etc/letsencrypt/live/platno.gogospace.cz ]; then
    exit 0
fi

/usr/sbin/apache2ctl configtest
/bin/systemctl reload apache2
