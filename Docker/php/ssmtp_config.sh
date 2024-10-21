#!/bin/sh

# Generate ssmtp.conf file
cat << EOF > /etc/ssmtp/ssmtp.conf
root=$SSMTP_EMAIL
mailhub=smtp.gmail.com:587
hostname=camagru
FromLineOverride=YES
AuthUser=$SSMTP_EMAIL
AuthPass=$SSMTP_AUTH_PASS
UseSTARTTLS=YES
Debug=YES
EOF

echo "SSMTP configuration complete."
