#!/bin/bash
openssl pkcs7 -inform DER -print_certs -text -in "$@"