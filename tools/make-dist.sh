#!/bin/bash -e

apt-get update
apt-get install -y php8.2-cli php8.2-zip php8.2-curl composer sassc zip --no-install-recommends

composer install -o

bash tools/compile-scss-sassc.sh

zip -9r ../cirrus-dist.zip -- *
