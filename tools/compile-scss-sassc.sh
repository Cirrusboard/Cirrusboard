#!/bin/sh

common_arguments="--style compressed --load-path ./"

mkdir -p assets/css

sassc ${common_arguments} assets/scss/style.scss assets/css/style.css
sassc ${common_arguments} themes/voxelmanip_retro/style.scss themes/voxelmanip_retro/style.css

# Compress compiled stylesheets for nginx gzip_static
gzip -fk assets/css/style.css
gzip -fk themes/voxelmanip_retro/style.css
