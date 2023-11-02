#!/bin/sh

common_arguments="--style compressed --load-path ./"

mkdir -p static/css

sassc ${common_arguments} scss/style.scss static/css/style.css
sassc ${common_arguments} static/themes/voxelmanip_retro/style.scss static/themes/voxelmanip_retro/style.css
