#!/bin/bash
# openssl smime -in "$1" -verify -inform DER -CAfile "$2" | grep "Verification"
openssl pkcs7 -in "$1" -inform DER -print_certs
