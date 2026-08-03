#!/usr/bin/env bash

case "$(uname -s)" in
  Darwin*)                  padHome=/Users/herbert/pad ;;
  Linux*)                   padHome=/home/herbert/pad  ;;
  MINGW*|MSYS*|CYGWIN*)     padHome=/c/pad             ;;
  *)                        echo "Unsupported OS: $(uname -s)" >&2; exit 1 ;;
esac
