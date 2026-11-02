# Finishing task worktrees

Use `scripts/finish-worktrees.py` to finish explicitly selected, inactive task branches:
back up their work, commit selected pending files, merge normally into the local target,
run one combined check, push the target, verify the remote commit, then remove the
integrated worktrees and their local branches. It uses Python's standard library and Git.
It does not discover branches to merge automatically, install a scheduler, or change Git
configuration. Select only branches belonging to the completed task; do not include
another person's active or unrelated work.

Run from the target worktree. `master` and `origin` are the defaults; use `--target main`
or `--remote NAME` when appropriate. The remote must already have the target branch.
Push uses the configured remote name after rechecking its single push URL, so Git also
updates tracking refs according to that remote's fetch mappings. No tracking names are invented.
An explicit branch list is required, and the default is a dry run:

```sh
python3 scripts/finish-worktrees.py codex/task-a codex/task-b --check './ci.sh'
python3 scripts/finish-worktrees.py --apply codex/task-a codex/task-b --check './ci.sh'
```

Once finishing these branches is authorized, the `--apply` invocation is the completion
step; the tool has no additional interactive confirmation. A dry run performs read-only
Git inspection, including a remote query, but creates no backups, commits, merges or
worktree removals. It cannot predict a merge conflict without performing the merge.

For a side worktree with pending changes, enumerate **every individual file**, including
untracked files and tracked deletions. Paths are relative to that side worktree and are
literal, not patterns. Renames need both the old and new paths. Directories are refused.

```sh
python3 scripts/finish-worktrees.py --apply codex/task-a \
  --include codex/task-a:pad/lib/example.php \
  --include codex/task-a:tests/example.php \
  --include codex/task-a:old-name.php \
  --check './ci.sh'
```

Only those paths are staged and committed. Partially staged side-worktree files use their
current working contents; both the original staged patch and working contents are backed
up first. An omitted pending file, an unnecessary selection, or a sensitive path stops the
run. `.env` and `.env.*`, private key files, credential files, and secret directories cannot
be automatically committed. This is a filename guard, not a content-based secret scanner;
review the selected changes before finishing. Ignored files are never committed.

## Validation and refusal conditions

`--check` is a single quoted command, parsed as arguments without a shell. It runs once
in the target worktree, after **all** merges and before push. For compound checks, provide
an existing script. Its nonzero exit stops before push and cleanup; source or index changes
made by a check also stop the run. A missing `--check` is printed as `none supplied`.
For an ancestry-only reconciliation with no source changes and an already completed
combined gate, omitting it avoids repeating that gate. Review a known unrelated failure
separately; do not replace a failing check with a command that silently returns success.

The utility stops for:

- A detached, locked, prunable, missing, or nested selected worktree; any in-progress
  merge, rebase, cherry-pick, revert, bisect, index lock, conflict, or submodule.
  Sparse worktrees and tracked files marked assume-unchanged or skip-worktree are refused because ordinary
  status cannot reliably expose their edits. Review and clear those flags manually first.
- Staged changes in the target worktree. Unrelated unstaged and untracked target files
  remain in place, with their contents checked before and after merging. No stash or reset
  is used. A side-branch change overlapping one of those paths, or an ignored target
  file, is refused; merges also use `--no-overwrite-ignore`.
- A remote target that is not an ancestor of the local target, a remote change during
  the run, a rejected push, or a remote verification mismatch. No force push is used.
- A side worktree that changes during processing, still has pending files after its
  commit, or is no longer integrated when cleanup begins.

The target worktree and branches named `main` or `master` are never removed. Selected
branches must each have an attached worktree. Only the local side branches are deleted;
remote side branches are not changed. Git hooks run normally; a side commit that includes
unexpected paths is detected and the run stops before merging it.

## Backups and recovery

Each apply run creates a private, unique directory under the repository's common Git
metadata directory, in `finish-worktrees-backups/`. `--backup-dir /safe/location` chooses
another parent outside all selected worktrees (or within the target Git metadata directory). The path is printed before any commit
or merge. Backups are retained after success or failure; the utility never deletes them.

The directory contains:

- `plan.json`: original branch tips, worktree paths, and explicit file selections.
- `before.bundle`: the original target and selected branch histories.
- `before-N/files.tar.gz` and `files.json`: dirty and untracked contents, deletions,
  symlinks, modes and hashes. Every archive is read back and checked against those
  hashes, types, symlink targets and modes before any work is considered backed up.
  Side-worktree backups also contain ignored files.
  `working.patch` and `index.patch` preserve the original working and staged changes.
- `committed.bundle` and `merged.bundle`, when those steps complete: histories including
  the newly committed pending work and the merged target.
- `cleanup-N/`: a fresh backup of ignored side-worktree files before removal.
- `complete.json`: the verified remote target commit, written only after cleanup succeeds.

Archives may contain private ignored files, so keep their directory private. They preserve
files and symlinks, not empty directories or extended filesystem attributes. Special files
and nested untracked repositories require manual handling. Backups of the target exclude
ignored files because that worktree is never removed.

If a merge conflicts, inspect and resolve or abort that merge normally; the tool leaves its
state, branch, worktree and backups available. If push fails, the local merges remain and
all worktrees are kept. A cleanup refusal can leave a previously removed worktree's branch
or some later worktrees intact; inspect the printed progress and `git worktree list` before
retrying. The tool never uses `merge -s ours`, a hard reset, `worktree remove --force`, or
`branch -D` to get past a refusal.

Inspect an archive before restoring files. To recover a deleted branch under a new name:

```sh
git bundle list-heads /safe/backup/committed.bundle
git fetch /safe/backup/committed.bundle \
  refs/heads/codex/task-a:refs/heads/codex/recovered-task-a
git worktree add /safe/recovered codex/recovered-task-a
```

Then restore the required files from its corresponding archive into that recovered
worktree, inspecting `files.json` for original deletions. Do not extract over current work
without reviewing it. Stop other writers while finishing: the utility checks for changes
and uses a process lock, but cannot prevent an editor or another Git process from changing
a worktree between checks. A stale `finish-worktrees.lock` needs inspection before manual
removal.

## Tests

```sh
python3 -m unittest discover -s tests/worktrees -v
```

These tests use temporary repositories and local bare remotes. They exercise the real Git
commit, merge, push and removal commands, including ignored-file recovery, preserved
unrelated target edits, rejected pushes, conflicts and the single combined validation hook.
They do not run PAD's full gate or access the network.
