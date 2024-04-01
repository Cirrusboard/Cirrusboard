#!/bin/bash

sudo apt-get update
sudo apt-get install -y php8.2-cli php8.2-zip php8.2-curl composer sassc zip --no-install-recommends

cd Cirrusboard

composer install -o

bash tools/compile-scss-sassc.sh

zip -9r ../cirrus-dist.zip -- *
