# Rule: timezone

Exact identifiers from DateTimeZone::ALL, including UTC. Backward compatibility aliases (US/Eastern), Etc aliases, numeric fixed offsets and case variants are rejected. The definition exposes the runtime tzdb version and supplies the exact server list to browser validation through the client argument hook, independent of browser Intl support.

Example: `padValidate(['value' => 'required|timezone'], ['value' => $value]);` The rule returns false on empty input itself; ordinary optional blank fields are skipped by PAD, so add `required` when blank values must fail. Submitted values remain unchanged. Every rule has a server error message and matching browser implementation. Run `php tests/subsystems/rule-timezone.php` and `node tests/subsystems/rule-timezone.js` for the shared fixtures.
