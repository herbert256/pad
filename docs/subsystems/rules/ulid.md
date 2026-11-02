# Rule: ulid

Canonical uppercase Crockford base32 only: 26 characters, first 0–7 to exclude 128-bit overflow; I/L/O/U and lowercase are rejected. No normalization.

Example: `padValidate(['value' => 'required|ulid'], ['value' => $value]);` The rule returns false on empty input itself; ordinary optional blank fields are skipped by PAD, so add `required` when blank values must fail. Submitted values remain unchanged. Every rule has a server error message and matching browser implementation. Run `php tests/subsystems/rule-ulid.php` and `node tests/subsystems/rule-ulid.js` for the shared fixtures.
