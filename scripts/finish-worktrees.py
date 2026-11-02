#!/usr/bin/env python3
"""Back up, merge, push and remove explicitly selected Git worktrees."""
import argparse
import hashlib
import json
import os
from pathlib import Path, PurePosixPath
import re
import shlex
import stat
import subprocess
import sys
import tarfile
import tempfile


class Stop(RuntimeError):
    pass


def git(repo, *args, check=True):
    result = subprocess.run(
        ['git', '--no-optional-locks', '--literal-pathspecs', '-C', str(repo), *args],
        stdout=subprocess.PIPE, stderr=subprocess.PIPE,
    )
    if check and result.returncode:
        # Git's diagnostic is useful, but do not echo commands containing remote URLs.
        detail = result.stderr.decode(errors='replace').strip()
        detail = re.sub(r'(https?://)[^/@\s]+:[^/@\s]+@', r'\1[redacted]@', detail)
        raise Stop(f"git {args[0]} failed: {detail}")
    return result


def output(repo, *args):
    return git(repo, *args).stdout.decode().strip()


def paths(repo, *args):
    return set(os.fsdecode(p) for p in git(repo, *args).stdout.split(b'\0') if p)


def inside(path, parent):
    return path == parent or parent in path.parents


def safe_path(value):
    p = PurePosixPath(value)
    if not value or p.is_absolute() or '..' in p.parts or '.git' in p.parts:
        raise Stop(f'Not a repository-relative file path: {value!r}')
    if str(p) != value or value.endswith('/'):
        raise Stop(f'Enumerate individual file paths, not directories: {value!r}')
    return value


def sensitive(path):
    for part in PurePosixPath(path).parts:
        name = part.lower()
        if (name == '.env' or name.startswith('.env.') or
                name in {'.ssh', '.aws', '.npmrc', '.netrc', 'secrets', 'credentials',
                         'id_rsa', 'id_ed25519', 'id_ecdsa'} or
                name.endswith(('.pem', '.key', '.p12', '.pfx')) or
                re.fullmatch(r'(secrets?|credentials?)[.-].*', name)):
            return True
    return False


def worktrees(repo):
    result = []
    current = {}
    for field in git(repo, 'worktree', 'list', '--porcelain', '-z').stdout.split(b'\0'):
        if not field:
            if current:
                result.append(current)
                current = {}
            continue
        key, _, value = os.fsdecode(field).partition(' ')
        current[key] = value
    if current:
        result.append(current)
    return result


def pending(repo):
    return (paths(repo, 'diff', '--name-only', '-z', '--no-renames', '--') |
            paths(repo, 'diff', '--cached', '--name-only', '-z', '--no-renames', '--') |
            paths(repo, 'ls-files', '--others', '--exclude-standard', '-z'))


def ignored(repo):
    return paths(repo, 'ls-files', '--others', '--ignored', '--exclude-standard', '-z')


def fingerprint(repo, names):
    result = {}
    for name in sorted(names):
        safe_path(name)
        p = repo / name
        try:
            s = p.lstat()
        except FileNotFoundError:
            result[name] = {'deleted': True}
            continue
        if stat.S_ISLNK(s.st_mode):
            digest = hashlib.sha256(os.fsencode(os.readlink(p))).hexdigest()
        elif stat.S_ISREG(s.st_mode):
            h = hashlib.sha256()
            with p.open('rb') as f:
                for chunk in iter(lambda: f.read(1024 * 1024), b''):
                    h.update(chunk)
            digest = h.hexdigest()
        else:
            raise Stop(f'Unsupported directory, nested repository or special file: {p}')
        result[name] = {'mode': stat.S_IMODE(s.st_mode), 'sha256': digest,
                        'symlink': stat.S_ISLNK(s.st_mode)}
    return result


def busy(repo):
    sparse = git(repo, 'config', '--bool', 'core.sparseCheckout', check=False)
    if sparse.returncode not in (0, 1) or sparse.stdout.strip() == b'true':
        raise Stop(f'Sparse worktrees need a separate workflow: {repo}')
    flagged = git(repo, 'ls-files', '-v', '-z').stdout.split(b'\0')
    if any(row and (row[:1].islower() or row[:1] == b'S') for row in flagged):
        raise Stop(f'Hidden tracked-file flags (assume-unchanged/skip-worktree) in {repo}; review and clear them first')
    for name in ['MERGE_HEAD', 'CHERRY_PICK_HEAD', 'REVERT_HEAD', 'rebase-merge',
                 'rebase-apply', 'sequencer', 'BISECT_START', 'index.lock']:
        location = Path(output(repo, 'rev-parse', '--git-path', name))
        if not location.is_absolute():
            location = repo / location
        if location.exists():
            raise Stop(f'In-progress Git operation at {repo}: {name}')
    if paths(repo, 'diff', '--name-only', '--diff-filter=U', '-z'):
        raise Stop(f'Unresolved conflicts in {repo}')
    if any(line.startswith(b'160000 ') for line in
           git(repo, 'ls-files', '--stage').stdout.splitlines()):
        raise Stop(f'Submodules need a separate workflow: {repo}')


def ancestor(repo, older, newer):
    r = git(repo, 'merge-base', '--is-ancestor', older, newer, check=False)
    if r.returncode not in (0, 1):
        raise Stop('Cannot determine commit ancestry')
    return r.returncode == 0


def remote_head(repo, url, target):
    lines = git(repo, 'ls-remote', '--refs', url, f'refs/heads/{target}').stdout.splitlines()
    if len(lines) != 1:
        raise Stop('The push destination must already contain exactly one target branch')
    return lines[0].split()[0].decode()


def write_json(path, data):
    path.write_text(json.dumps(data, indent=2, ensure_ascii=True) + '\n')


def verify_archive(path, state):
    expected = {name: entry for name, entry in state.items() if not entry.get('deleted')}
    with tarfile.open(path, 'r:gz') as archive:
        members = archive.getmembers()
        if len(members) != len(expected) or {m.name for m in members} != set(expected):
            raise Stop('Backup archive member list does not match its fingerprint')
        for member in members:
            entry = expected[member.name]
            if member.mode != entry['mode']:
                raise Stop(f'Backup archive mode mismatch: {member.name!r}')
            h = hashlib.sha256()
            if entry['symlink']:
                if not member.issym():
                    raise Stop(f'Backup archive symlink mismatch: {member.name!r}')
                h.update(os.fsencode(member.linkname))
            else:
                if not member.isfile():
                    raise Stop(f'Backup archive file type mismatch: {member.name!r}')
                with archive.extractfile(member) as stream:
                    for chunk in iter(lambda: stream.read(1024 * 1024), b''):
                        h.update(chunk)
            if h.hexdigest() != entry['sha256']:
                raise Stop(f'Backup archive content mismatch: {member.name!r}')


def backup_files(repo, folder, include_ignored=False):
    folder.mkdir()
    names = pending(repo) | (ignored(repo) if include_ignored else set())
    state = fingerprint(repo, names)
    write_json(folder / 'files.json', state)
    for args, filename in [(['diff', '--binary', 'HEAD', '--'], 'working.patch'),
                           (['diff', '--cached', '--binary', '--'], 'index.patch')]:
        (folder / filename).write_bytes(git(repo, *args).stdout)
    with tarfile.open(folder / 'files.tar.gz', 'w:gz', dereference=False) as archive:
        for name, entry in state.items():
            if not entry.get('deleted'):
                archive.add(repo / name, arcname=name, recursive=False)
    verify_archive(folder / 'files.tar.gz', state)
    if fingerprint(repo, names) != state or names != (pending(repo) | (ignored(repo) if include_ignored else set())):
        raise Stop(f'Files changed while backing up {repo}; rerun when writers are stopped')
    return state


def bundle(repo, folder, filename, branches):
    destination = folder / filename
    git(repo, 'bundle', 'create', str(destination), *[f'refs/heads/{b}' for b in branches])
    git(repo, 'bundle', 'verify', str(destination))


def main(argv=None):
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('branches', nargs='+', help='Explicit local side branches to finish')
    parser.add_argument('--repo', default='.', help='Worktree of the target branch (default: current directory)')
    parser.add_argument('--target', default='master')
    parser.add_argument('--remote', default='origin')
    parser.add_argument('--include', action='append', default=[], metavar='BRANCH:FILE',
                        help='Exact dirty/untracked file to commit; repeat for every file, including deletions')
    parser.add_argument('--backup-dir', help='Parent directory for a private, unique backup folder')
    parser.add_argument('--check', metavar='COMMAND',
                        help='Run this one combined check after all merges, before push (quoted argv; no shell)')
    parser.add_argument('--apply', action='store_true', help='Perform the printed plan; otherwise no writes')
    args = parser.parse_args(argv)
    repo = Path(output(Path(args.repo).resolve(), 'rev-parse', '--show-toplevel')).resolve()
    branches = args.branches
    if len(set(branches)) != len(branches):
        raise Stop('Duplicate branches')
    for branch in [args.target, *branches]:
        git(repo, 'check-ref-format', '--branch', branch)
    if args.target in branches or {'main', 'master'} & set(branches):
        raise Stop('Never remove the target, main or master worktree')
    if output(repo, 'branch', '--show-current') != args.target:
        raise Stop('Run from the checked-out target branch; detached targets are refused')
    selected = {}
    inventory = worktrees(repo)
    for branch in [args.target, *branches]:
        matches = [w for w in inventory if w.get('branch') == f'refs/heads/{branch}']
        if len(matches) != 1:
            raise Stop(f'{branch}: expected one attached worktree, not a detached/missing branch')
        w = matches[0]
        if 'locked' in w or 'prunable' in w or 'detached' in w:
            raise Stop(f'{branch}: locked, detached or prunable worktree')
        p = Path(w['worktree']).resolve()
        if branch == args.target and p != repo:
            raise Stop('Target worktree mismatch')
        busy(p)
        selected[branch] = p
    for i, first in enumerate(selected.values()):
        for second in list(selected.values())[i + 1:]:
            if inside(first, second) or inside(second, first):
                raise Stop('Selected worktrees must not contain one another')
    if git(repo, 'diff', '--cached', '--quiet', check=False).returncode:
        raise Stop('Target has staged changes; keep its index untouched and finish them first')
    wanted = {b: set() for b in branches}
    for item in args.include:
        branch, sep, name = item.partition(':')
        if not sep or branch not in wanted:
            raise Stop('--include must name one selected branch and one exact file')
        wanted[branch].add(safe_path(name))
    target_pending = pending(repo)
    target_ignored = ignored(repo)
    target_state = fingerprint(repo, target_pending)
    initial_heads = {b: output(p, 'rev-parse', 'HEAD') for b, p in selected.items()}
    for branch in branches:
        p = selected[branch]
        changes = pending(p)
        if changes != wanted[branch]:
            missing = sorted(changes - wanted[branch])
            extra = sorted(wanted[branch] - changes)
            raise Stop(f'{branch}: enumerate every pending file with --include; missing={missing!r}, extra={extra!r}')
        committed = paths(repo, 'diff', '--name-only', '--no-renames', '-z', f'{args.target}...{branch}', '--')
        if any(sensitive(name) for name in changes | committed):
            raise Stop(f'{branch}: sensitive path detected; review it manually (never auto-commit secrets)')
        if changes & ignored(p):
            raise Stop(f'{branch}: ignored files cannot be included')
        for dirty in target_pending | target_ignored:
            if any(dirty == n or dirty.startswith(n + '/') or n.startswith(dirty + '/')
                   for n in changes | committed):
                raise Stop(f'{branch}: merge overlaps a dirty or ignored target path: {dirty!r}')
        fingerprint(p, changes | ignored(p))
    urls = output(repo, 'remote', 'get-url', '--push', '--all', args.remote).splitlines()
    if len(urls) != 1:
        raise Stop('Exactly one push URL is required')
    url = urls[0]
    remote = remote_head(repo, url, args.target)
    known = git(repo, 'cat-file', '-e', remote + '^{commit}', check=False).returncode == 0
    if known and not ancestor(repo, remote, args.target):
        raise Stop('Remote target is not an ancestor of local target; refusing a non-fast-forward finish')
    common = Path(output(repo, 'rev-parse', '--git-common-dir'))
    if not common.is_absolute():
        common = repo / common
    common = common.resolve()
    parent = Path(args.backup_dir).expanduser().resolve() if args.backup_dir else common / 'finish-worktrees-backups'
    if inside(parent, repo) and not inside(parent, common):
        raise Stop('--backup-dir must be outside the target worktree or within its Git metadata directory')
    if any(inside(parent, selected[b]) for b in branches):
        raise Stop('Backups must be outside every worktree being removed')
    print(f'{"APPLY" if args.apply else "DRY RUN"}: merge {", ".join(branches)} into {args.target}; push {args.remote}; verify; remove selected worktrees and branches')
    for branch in branches:
        print(f'  {branch}: {len(wanted[branch])} explicitly selected pending files; ignored files backed up, never committed')
    print(f'Backup parent: {parent}')
    check_command = shlex.split(args.check) if args.check is not None else None
    if check_command == []:
        raise Stop('--check requires a nonempty command')
    print(f'Combined check: {args.check or "none supplied"}')
    if not known:
        print('Remote ancestry requires fetching its commit during --apply before any merge.')
    if not args.apply:
        return 0

    lock = common / 'finish-worktrees.lock'
    try:
        fd = os.open(lock, os.O_WRONLY | os.O_CREAT | os.O_EXCL, 0o600)
    except FileExistsError:
        raise Stop(f'Another finish may be running: {lock}; inspect before removing a stale lock')
    backup = None
    try:
        with os.fdopen(fd, 'w') as f:
            f.write(str(os.getpid()) + '\n')
        parent.mkdir(parents=True, exist_ok=True, mode=0o700)
        backup = Path(tempfile.mkdtemp(prefix='finish-', dir=parent))
        os.chmod(backup, 0o700)
        print(f'Backup: {backup}', flush=True)
        write_json(backup / 'plan.json', {'target': args.target, 'branches': branches, 'heads': initial_heads,
                                         'worktrees': {b: str(p) for b, p in selected.items()},
                                         'include': {b: sorted(v) for b, v in wanted.items()}})
        bundle(repo, backup, 'before.bundle', [args.target, *branches])
        saved = {}
        for number, (branch, p) in enumerate(selected.items()):
            saved[branch] = backup_files(p, backup / f'before-{number}', branch != args.target)
        git(repo, 'fetch', '--no-tags', '--no-write-fetch-head', url, f'refs/heads/{args.target}')
        if remote_head(repo, url, args.target) != remote or not ancestor(repo, remote, args.target):
            raise Stop('Remote moved or would require a non-fast-forward update; no merge performed')
        for branch, p in selected.items():
            busy(p)
            if (output(p, 'rev-parse', 'HEAD') != initial_heads[branch] or
                    output(p, 'branch', '--show-current') != branch):
                raise Stop(f'{branch} moved during backup')
            names = pending(p) | (ignored(p) if branch != args.target else set())
            if fingerprint(p, names) != saved[branch]:
                raise Stop(f'{branch}: files changed after backup')
        if pending(repo) != target_pending or fingerprint(repo, target_pending) != target_state:
            raise Stop('Target files changed during backup')
        for branch in branches:
            p = selected[branch]
            if pending(p) != wanted[branch]:
                raise Stop(f'{branch}: pending file list changed')
            if wanted[branch]:
                git(p, 'add', '--', *sorted(wanted[branch]))
                staged = paths(p, 'diff', '--cached', '--name-only', '--no-renames', '-z')
                if staged != wanted[branch]:
                    raise Stop(f'{branch}: staged files differ from explicit selection')
                git(p, 'commit', '-m', f'Finish pending work on {branch}')
                actual = paths(p, 'diff-tree', '--no-commit-id', '--name-only', '--no-renames', '-r', '-z', 'HEAD')
                if actual != wanted[branch]:
                    raise Stop(f'{branch}: commit hook changed the selection; review before merging')
            if pending(p) or output(p, 'branch', '--show-current') != branch:
                raise Stop(f'{branch}: dirty files or a changed checkout remain after commit')
        bundle(repo, backup, 'committed.bundle', [args.target, *branches])
        final_heads = {b: output(selected[b], 'rev-parse', 'HEAD') for b in branches}
        for branch in branches:
            if git(repo, 'diff', '--cached', '--quiet', check=False).returncode:
                raise Stop('Target index changed before merge')
            git(repo, 'merge', '--no-ff', '--no-edit', '--no-overwrite-ignore', '--', branch)
            if pending(repo) != target_pending or fingerprint(repo, target_pending) != target_state:
                raise Stop('Target edits changed during merge; inspect backup before continuing')
        tip = output(repo, 'rev-parse', 'HEAD')
        bundle(repo, backup, 'merged.bundle', [args.target, *branches])
        if check_command:
            print('Running combined validation', flush=True)
            if subprocess.run(check_command, cwd=repo).returncode:
                raise Stop('Combined validation failed; no push or cleanup performed')
        if (output(repo, 'rev-parse', 'HEAD') != tip or
                git(repo, 'diff', '--cached', '--quiet', check=False).returncode or
                pending(repo) != target_pending or fingerprint(repo, target_pending) != target_state):
            raise Stop('Target changed during validation; no push or cleanup performed')
        if remote_head(repo, url, args.target) != remote:
            raise Stop('Remote moved since preflight; worktrees retained')
        if output(repo, 'remote', 'get-url', '--push', '--all', args.remote).splitlines() != [url]:
            raise Stop('Configured push destination changed; worktrees retained')
        # Let Git update tracking refs through this remote's configured fetch refspecs.
        git(repo, 'push', args.remote, f'refs/heads/{args.target}:refs/heads/{args.target}')
        if remote_head(repo, url, args.target) != tip:
            raise Stop('Remote verification did not match local target; worktrees retained')
        # Validate every candidate before removing any. Never use worktree --force or branch -D.
        cleanup_states = {}
        for number, branch in enumerate(branches):
            p = selected[branch]
            busy(p)
            if (output(p, 'rev-parse', 'HEAD') != final_heads[branch] or
                    output(p, 'branch', '--show-current') != branch or pending(p) or
                    not ancestor(repo, branch, args.target)):
                raise Stop(f'{branch}: changed, dirty or unmerged after push; worktrees retained')
            state = backup_files(p, backup / f'cleanup-{number}', True)
            cleanup_states[branch] = state
            if state != fingerprint(p, ignored(p)):
                raise Stop(f'{branch}: ignored files changed during cleanup backup')
        for branch in branches:
            p = selected[branch]
            busy(p)
            if (pending(p) or output(p, 'rev-parse', 'HEAD') != final_heads[branch] or
                    output(p, 'branch', '--show-current') != branch or
                    fingerprint(p, ignored(p)) != cleanup_states[branch]):
                raise Stop(f'{branch}: changed before removal')
            git(repo, 'worktree', 'remove', str(p))
            git(repo, 'branch', '-d', '--', branch)
            print(f'Removed {branch} and its clean integrated worktree')
        write_json(backup / 'complete.json', {'target': args.target, 'commit': tip, 'remoteVerified': True})
        return 0
    finally:
        lock.unlink(missing_ok=True)
        if backup:
            print(f'Recovery material retained: {backup}')


if __name__ == '__main__':
    try:
        sys.exit(main())
    except (Stop, OSError, ValueError, tarfile.TarError) as error:
        print(f'STOP: {error}', file=sys.stderr)
        sys.exit(1)
