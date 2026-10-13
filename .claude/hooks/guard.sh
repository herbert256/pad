#!/bin/bash

# PreToolUse guard for this shared checkout - Bash and the pad_test MCP tool.
# Exit 2 refuses the call; the message goes back to Claude.

in=$(cat)
tool=$(jq -r '.tool_name' <<<"$in")
cmd=$(jq -r '.tool_input.command // ""' <<<"$in")

# The checkout is shared with other sessions and builds: commit by path, never the whole tree.

if grep -Eq 'git( -C [^ ]+)? (add (-A|--all|\.)( |$)|commit (-[a-zA-Z]*a|--all))' <<<"$cmd"; then
  echo "Commit by path: git commit -m \"...\" -- <paths>. The checkout is shared - git add -A, add . and commit -a would take another session's changes along." >&2
  exit 2
fi

# One gate at a time: two ./ci.sh runs read each other's suite results as another run's
# and break each other's fixtures. pad_test spawns bash ci.sh, so pgrep sees it too.

if [ "$tool" != Bash ] || grep -Eq '\./ci\.sh|bash .*ci\.sh' <<<"$cmd"; then
  if pgrep -f 'ci\.sh' > /dev/null; then
    echo "A ./ci.sh is already running ($(pgrep -f 'ci\.sh' | tr '\n' ' ')) - two gates at once break each other. Wait until it ends." >&2
    exit 2
  fi
fi

exit 0
