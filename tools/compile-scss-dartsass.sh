#!/bin/sh -e

sass --style=compressed --no-source-map --watch scss/:static/css/ static/themes/*/
