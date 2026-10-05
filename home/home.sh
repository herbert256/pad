#!/usr/bin/env bash

case "$(uname -s)" in
  Darwin*)                  padHome=/Users/herbert/pad ;;
  Linux*)                   padHome=/home/herbert/pad  ;;
  MINGW*|MSYS*|CYGWIN*)     padHome=/c/pad             ;;
  *)                        echo "Unsupported OS: $(uname -s)" >&2; exit 1 ;;
esac

# A second checkout (a git worktree with its own php -S server) says where it lives.
if [ -n "$PAD_HOME" ]; then padHome="${PAD_HOME%/}"; fi

