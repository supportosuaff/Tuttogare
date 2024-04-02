#!/bin/bash
openssl smime -verify -inform DER -in "$1" -noverify -out "$2"