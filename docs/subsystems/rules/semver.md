# Rule: semver

The Semantic Versioning 2.0.0 grammar: three nonnegative integer components without leading zeros, optional dot-separated prerelease and build identifiers. Numeric prerelease identifiers forbid leading zeros; build identifiers allow them. No v prefix or whitespace.

Example: `padValidate(['value' => 'required|semver'], ['value' => $value]);` The rule returns false on empty input itself; ordinary optional blank fields are skipped by PAD, so add `required` when blank values must fail. Submitted values remain unchanged. Every rule has a server error message and matching browser implementation. Run `php tests/subsystems/rule-semver.php` and `node tests/subsystems/rule-semver.js` for the shared fixtures.
