# Fake: semver

`padFake('semver', ['major' => [1, 3], 'minor' => 0, 'patch' => [0, 20], 'prerelease' => 'rc.1', 'build' => 'test.001'])` produces Semantic Versioning 2.0.0 values. Component options are an integer or inclusive [min,max] pair, bounded to one billion. Defaults: major 0–10, minor 0–20, patch 0–100. Optional suffixes are validated, not normalized.

Template equivalent: `{fake:semver/}`, with the same named options. Errors call `padError` and return NULL if execution continues. Run `php tests/subsystems/fake-semver.php`.
