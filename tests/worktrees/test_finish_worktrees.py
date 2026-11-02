"""Hermetic end-to-end tests: only temporary repositories and local bare remotes."""
from pathlib import Path
import json
import hashlib
import importlib.util
import io
import os
import subprocess
import sys
import tarfile
import tempfile
import unittest

SCRIPT = Path(__file__).resolve().parents[2] / 'scripts/finish-worktrees.py'
SPEC = importlib.util.spec_from_file_location('finish_worktrees', SCRIPT)
FINISH = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(FINISH)


class FinishWorktrees(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        self.repo = self.root / 'main'
        self.remote = self.root / 'remote.git'
        self.backups = self.root / 'backups'
        self.env = os.environ.copy()
        # No personal Git settings, hooks, credential helpers or inherited repository state.
        for key in list(self.env):
            if key.startswith('GIT_'):
                del self.env[key]
        self.env.update(GIT_CONFIG_NOSYSTEM='1', GIT_CONFIG_GLOBAL=os.devnull,
                        GIT_AUTHOR_NAME='Test', GIT_AUTHOR_EMAIL='test@example.invalid',
                        GIT_COMMITTER_NAME='Test', GIT_COMMITTER_EMAIL='test@example.invalid',
                        GIT_TERMINAL_PROMPT='0')
        self.run_git(self.root, 'init', '--bare', str(self.remote))
        self.run_git(self.root, 'init', '-b', 'master', str(self.repo))
        (self.repo / '.gitignore').write_text('ignored/\n')
        (self.repo / 'base.txt').write_text('base\n')
        (self.repo / 'unrelated.txt').write_text('original\n')
        self.run_git(self.repo, 'add', '--all')
        self.run_git(self.repo, 'commit', '-m', 'base')
        self.run_git(self.repo, 'remote', 'add', 'origin', str(self.remote))
        self.run_git(self.repo, 'push', '-u', 'origin', 'master')
        self.initial = self.run_git(self.repo, 'rev-parse', 'HEAD').stdout.strip()

    def run_git(self, repo, *args, check=True):
        r = subprocess.run(['git', '-C', str(repo), *args], env=self.env,
                           capture_output=True, text=True)
        if check:
            self.assertEqual(r.returncode, 0, r.stderr)
        return r

    def side(self, name='codex/feature'):
        path = self.root / name.replace('/', '-')
        self.run_git(self.repo, 'worktree', 'add', '-b', name, str(path))
        return path

    def commit(self, repo, filename, text):
        (repo / filename).write_text(text)
        self.run_git(repo, 'add', '--', filename)
        self.run_git(repo, 'commit', '-m', filename)

    def finish(self, *args):
        return subprocess.run([sys.executable, str(SCRIPT), '--repo', str(self.repo),
                               '--backup-dir', str(self.backups), *args],
                              env=self.env, capture_output=True, text=True)

    def remote_tip(self):
        return self.run_git(self.remote, 'rev-parse', 'master').stdout.strip()

    def branch_exists(self, branch):
        return self.run_git(self.repo, 'show-ref', '--verify', 'refs/heads/' + branch,
                            check=False).returncode == 0

    def test_finish_commits_exact_files_preserves_main_and_ignored_backup(self):
        side = self.side()
        self.commit(side, 'feature.txt', 'committed branch work\n')
        (side / 'feature.txt').write_text('pending branch work\n')
        (side / 'new file.txt').write_text('untracked addition\n')
        (side / 'ignored').mkdir()
        (side / 'ignored/private.cache').write_bytes(b'ignored bytes\x00\x01')
        (self.repo / 'unrelated.txt').write_text('user edits\n')
        (self.repo / 'user.txt').write_text('user untracked\n')
        before = self.run_git(self.repo, 'status', '--porcelain').stdout
        r = self.finish('--apply', 'codex/feature', '--include', 'codex/feature:feature.txt',
                        '--include', 'codex/feature:new file.txt')
        self.assertEqual(r.returncode, 0, r.stdout + r.stderr)
        self.assertFalse(side.exists())
        self.assertFalse(self.branch_exists('codex/feature'))
        self.assertEqual((self.repo / 'feature.txt').read_text(), 'pending branch work\n')
        self.assertEqual((self.repo / 'new file.txt').read_text(), 'untracked addition\n')
        self.assertEqual((self.repo / 'unrelated.txt').read_text(), 'user edits\n')
        self.assertEqual((self.repo / 'user.txt').read_text(), 'user untracked\n')
        self.assertEqual(self.run_git(self.repo, 'status', '--porcelain').stdout, before)
        self.assertEqual(self.remote_tip(), self.run_git(self.repo, 'rev-parse', 'HEAD').stdout.strip())
        self.assertEqual(self.run_git(self.repo, 'rev-parse', 'refs/remotes/origin/master').stdout.strip(),
                         self.run_git(self.repo, 'rev-parse', 'master').stdout.strip())
        backup, = self.backups.iterdir()
        self.assertTrue((backup / 'complete.json').exists())
        self.run_git(self.repo, 'bundle', 'verify', str(backup / 'merged.bundle'))
        with tarfile.open(backup / 'before-1/files.tar.gz') as archive:
            self.assertEqual(archive.extractfile('ignored/private.cache').read(), b'ignored bytes\x00\x01')
        self.assertEqual(json.loads((backup / 'before-0/files.json').read_text()).keys(),
                         {'unrelated.txt', 'user.txt'})

    def test_dry_run_does_not_commit_backup_merge_push_or_remove(self):
        side = self.side()
        self.commit(side, 'feature.txt', 'feature\n')
        r = self.finish('codex/feature')
        self.assertEqual(r.returncode, 0, r.stderr)
        self.assertIn('DRY RUN', r.stdout)
        self.assertFalse(self.backups.exists())
        self.assertTrue(side.exists())
        self.assertEqual(self.remote_tip(), self.initial)
        self.assertEqual(self.run_git(self.repo, 'rev-parse', 'HEAD').stdout.strip(), self.initial)

    def test_merge_conflict_keeps_branch_worktree_and_remote(self):
        side = self.side()
        self.commit(side, 'base.txt', 'branch change\n')
        self.commit(self.repo, 'base.txt', 'target change\n')
        r = self.finish('--apply', 'codex/feature')
        self.assertNotEqual(r.returncode, 0)
        self.assertIn('git merge failed', r.stderr)
        self.assertTrue(side.exists())
        self.assertTrue(self.branch_exists('codex/feature'))
        self.assertTrue((self.repo / '.git/MERGE_HEAD').exists())
        self.assertEqual(self.remote_tip(), self.initial)
        self.assertTrue(list(self.backups.glob('*/before.bundle')))

    def test_push_rejection_keeps_integrated_worktree_and_branch(self):
        side = self.side()
        self.commit(side, 'feature.txt', 'feature\n')
        hook = self.remote / 'hooks/pre-receive'
        hook.write_text('#!/bin/sh\necho rejected-for-test >&2\nexit 1\n')
        hook.chmod(0o700)
        r = self.finish('--apply', 'codex/feature')
        self.assertNotEqual(r.returncode, 0)
        self.assertIn('git push failed', r.stderr)
        self.assertTrue(side.exists())
        self.assertTrue(self.branch_exists('codex/feature'))
        self.assertEqual(self.remote_tip(), self.initial)
        self.run_git(self.repo, 'merge-base', '--is-ancestor', 'codex/feature', 'master')

    def test_combined_check_runs_once_after_all_merges(self):
        one = self.side('codex/one')
        two = self.side('codex/two')
        self.commit(one, 'one.txt', 'one\n')
        self.commit(two, 'two.txt', 'two\n')
        count = self.root / 'checks.txt'
        check = self.root / 'check.py'
        check.write_text('from pathlib import Path\nassert Path("one.txt").exists()\n'
                         'assert Path("two.txt").exists()\n'
                         f'with Path({str(count)!r}).open("a") as f: f.write("once\\n")\n')
        r = self.finish('--apply', '--check', f'{sys.executable} {check}', 'codex/one', 'codex/two')
        self.assertEqual(r.returncode, 0, r.stdout + r.stderr)
        self.assertEqual(count.read_text(), 'once\n')
        self.assertFalse(one.exists())
        self.assertFalse(two.exists())

    def test_failed_check_stops_before_push_and_cleanup(self):
        side = self.side()
        self.commit(side, 'feature.txt', 'feature\n')
        check = self.root / 'fail.py'
        check.write_text('raise SystemExit(17)\n')
        r = self.finish('--apply', '--check', f'{sys.executable} {check}', 'codex/feature')
        self.assertNotEqual(r.returncode, 0)
        self.assertIn('Combined validation failed', r.stderr)
        self.assertEqual(self.remote_tip(), self.initial)
        self.assertTrue(side.exists())

    def test_refuses_missing_selection_sensitive_path_and_staged_target(self):
        side = self.side()
        (side / 'new.txt').write_text('new\n')
        r = self.finish('--apply', 'codex/feature')
        self.assertIn('enumerate every pending file', r.stderr)
        (side / 'new.txt').unlink()
        (side / '.env').write_text('TOKEN=test\n')
        r = self.finish('--apply', '--include', 'codex/feature:.env', 'codex/feature')
        self.assertIn('sensitive path', r.stderr)
        (side / '.env').unlink()
        (self.repo / 'unrelated.txt').write_text('staged user\n')
        self.run_git(self.repo, 'add', 'unrelated.txt')
        r = self.finish('--apply', 'codex/feature')
        self.assertIn('Target has staged changes', r.stderr)
        self.assertFalse(self.backups.exists())
        self.assertTrue(side.exists())

    def test_remote_ahead_refused_before_merge(self):
        side = self.side()
        self.commit(side, 'feature.txt', 'feature\n')
        other = self.root / 'other'
        self.run_git(self.root, 'clone', str(self.remote), str(other))
        self.commit(other, 'remote.txt', 'other writer\n')
        self.run_git(other, 'push', 'origin', 'master')
        remote = self.remote_tip()
        r = self.finish('--apply', 'codex/feature')
        self.assertNotEqual(r.returncode, 0)
        self.assertIn('non-fast-forward', r.stderr)
        self.assertEqual(self.remote_tip(), remote)
        self.assertEqual(self.run_git(self.repo, 'rev-parse', 'HEAD').stdout.strip(), self.initial)
        self.assertTrue(side.exists())


    def test_locked_detached_and_protected_worktrees_are_refused(self):
        side = self.side()
        self.run_git(self.repo, 'worktree', 'lock', str(side))
        r = self.finish('--apply', 'codex/feature')
        self.assertIn('locked', r.stderr)
        self.run_git(self.repo, 'worktree', 'unlock', str(side))
        self.run_git(side, 'checkout', '--detach')
        r = self.finish('--apply', 'codex/feature')
        self.assertIn('detached/missing', r.stderr)
        r = self.finish('--apply', 'master')
        self.assertIn('Never remove', r.stderr)
        self.assertTrue(side.exists())
        self.assertFalse(self.backups.exists())

    def test_ignored_target_file_is_not_overwritten(self):
        side = self.side()
        (side / 'ignored').mkdir()
        (side / 'ignored/collision.txt').write_text('branch version\n')
        self.run_git(side, 'add', '-f', 'ignored/collision.txt')
        self.run_git(side, 'commit', '-m', 'tracked in branch')
        (self.repo / 'ignored').mkdir()
        original = b'user ignored work\x00'
        (self.repo / 'ignored/collision.txt').write_bytes(original)
        r = self.finish('--apply', 'codex/feature')
        self.assertIn('ignored target path', r.stderr)
        self.assertEqual((self.repo / 'ignored/collision.txt').read_bytes(), original)
        self.assertEqual(self.remote_tip(), self.initial)
        self.assertTrue(side.exists())
        self.assertFalse(self.backups.exists())

    def test_hidden_tracked_edits_are_refused_in_side_and_target(self):
        side = self.side()
        for repo in [side, self.repo]:
            for flag in ['assume-unchanged', 'skip-worktree']:
                with self.subTest(repo=str(repo), flag=flag):
                    self.run_git(repo, 'update-index', '--' + flag, 'base.txt')
                    (repo / 'base.txt').write_text('hidden user edits\n')
                    r = self.finish('--apply', 'codex/feature')
                    self.assertIn('Hidden tracked-file flags', r.stderr)
                    self.assertEqual((repo / 'base.txt').read_text(), 'hidden user edits\n')
                    self.assertTrue(side.exists())
                    self.run_git(repo, 'update-index', '--no-' + flag, 'base.txt')
                    (repo / 'base.txt').write_text('base\n')
        self.assertFalse(self.backups.exists())

    def test_archive_verifier_rejects_wrong_bytes_link_and_mode(self):
        path = self.root / 'probe.tar.gz'
        state = {
            'file.txt': {'sha256': hashlib.sha256(b'correct').hexdigest(), 'mode': 0o640, 'symlink': False},
            'link': {'sha256': hashlib.sha256(b'file.txt').hexdigest(), 'mode': 0o777, 'symlink': True},
        }
        for defect in ['bytes', 'link', 'mode', None]:
            with self.subTest(defect=defect):
                with tarfile.open(path, 'w:gz') as archive:
                    body = b'wrong' if defect == 'bytes' else b'correct'
                    info = tarfile.TarInfo('file.txt')
                    info.mode = 0o600 if defect == 'mode' else 0o640
                    info.size = len(body)
                    archive.addfile(info, io.BytesIO(body))
                    link = tarfile.TarInfo('link')
                    link.type = tarfile.SYMTYPE
                    link.mode = 0o777
                    link.linkname = 'other.txt' if defect == 'link' else 'file.txt'
                    archive.addfile(link)
                if defect:
                    with self.assertRaises(FINISH.Stop):
                        FINISH.verify_archive(path, state)
                else:
                    FINISH.verify_archive(path, state)

    def test_unignored_backup_directory_inside_target_is_refused(self):
        side = self.side()
        r = self.finish('--apply', '--backup-dir', str(self.repo / 'new-backups'), 'codex/feature')
        self.assertIn('outside the target worktree', r.stderr)
        self.assertFalse((self.repo / 'new-backups').exists())
        self.assertTrue(side.exists())


if __name__ == '__main__':
    unittest.main()
