#!/bin/sh -e

common_arguments="--style compressed --load-path ./"

mkdir -p static/css

sassc ${common_arguments} scss/style.scss static/css/style.css
