---
name: pad-commit
description: Gate and commit one PAD fix on master - check that no other gate runs, ./ci.sh green, commit by path with the repository's one-sentence message, mark the hunt note. Use whenever a PAD fix, with its regression case, is ready to commit.
---

# Committing one PAD fix

One fix, one commit, straight on master. Never push unless the user asked for it.

## 1. What goes in

- `git status --short` and `git log -1 --oneline`. The checkout is shared with other sessions
  and with develop builds: HEAD can have moved, and files you did not touch can be modified.
- The commit holds only the paths you changed for this fix: the engine file(s) and the
  regression case (`.pad`, `.php`, `.txt`). A modified path you did not change belongs to
  someone else - leave it out, even when it looks related.
- `git diff -- <your paths>` and read it: no debug output, no stray probe.
- No scratch page left under `apps/` (the Other suite crawls every page there and fails on
  one without an answer). Probes belong in `DATA/hunt/scratch-*/` or the scratchpad.

## 2. The case fails on the old engine

The regression case must fail on HEAD and pass on the working tree:
`pad_compare app=regression/framework page=<group>/<case> bare=true` (or the suite's own app)
- the old side differs from the case's `.txt`, the new side equals it. `pad_case` judges it as
its suite does.

## 3. The gate

- `pgrep -fl 'ci\.sh'` first. If a gate is running, wait for it to end - two at once read each
  other's results as "another run's" and break each other's fixtures.
- `./ci.sh` (or `pad_test` with no suite). Commit only on exit 0.
- A red line that is not yours - a suite "belongs to another run", a failing test in a file
  another session is changing - is still red: rerun once the other run is over; do not commit
  over it.
- A new tag, function or option name: the `editors` line needs `php editors/generate.php`, and
  `apps/regression/other/reference/index.txt` changes - record it with the pad-record-answer
  skill after checking that only the new name was added.
- A stale `.git/index.lock` (a ci run shells out to git) blocks the commit: when `ps aux | grep
  '[g]it '` shows no git process, remove it.

## 4. The commit

By path, never `git add -A`, `git add .` or `git commit -a`:

```bash
git add <new case files>
git commit -m "$(cat <<'EOF'
<What happens now, as one sentence>, where <what happened before, with the input that showed it> (<tag: where the finding came from>)

Co-Authored-By: <the attribution line this session gives>
EOF
)" -- <every path of this fix>
```

The message is one long descriptive sentence, as `git log` shows: the behaviour now first,
then `where` and the old behaviour with a concrete example, then a parenthesised tag naming
the source, e.g. `(review of hunt4: pdf ol start counts from 0)`.

## 5. After

- When the fix came from a findings file: `python3 DATA/hunt/mark.py <stem> <section> <hash>`
  appends `- Commit: <hash>` to that `## ` section of `DATA/hunt/<stem>.md` (sections counted
  from 1, in file order).
- `git status --short` once more: your paths are clean, nothing else of yours is left.
- Report the hash, what the fix does, and that the gate was green.
