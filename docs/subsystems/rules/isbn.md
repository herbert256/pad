# Rule: isbn

ISBN-10 modulo-11 and ISBN-13 alternating 1/3 checksum, with 978/979 prefixes. Removes ASCII spaces and hyphens for validation only. ISBN-10 permits uppercase X only in its last position. Does not assert that an ISBN has been assigned.

Example: `padValidate(['value' => 'required|isbn'], ['value' => $value]);` The rule returns false on empty input itself; ordinary optional blank fields are skipped by PAD, so add `required` when blank values must fail. Submitted values remain unchanged. Every rule has a server error message and matching browser implementation. Run `php tests/subsystems/rule-isbn.php` and `node tests/subsystems/rule-isbn.js` for the shared fixtures.
