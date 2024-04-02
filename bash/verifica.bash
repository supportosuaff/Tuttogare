#!/bin/bash
openssl smime -in "$1" -verify -inform DER -CAfile "$2" | grep "Verification"