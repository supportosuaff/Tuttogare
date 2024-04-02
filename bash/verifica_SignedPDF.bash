#!/bin/bash
# openssl smime -in "$1" -verify -inform DER -CAfile "$2" | grep "Verification"
openssl verify -partial_chain -verbose -CAfile "$2" "$1" | grep ": OK"
