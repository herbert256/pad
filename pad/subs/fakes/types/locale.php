<?php

  // locale - `supported=` is an explicit nonempty array of 1–200 BCP 47-shaped identifiers; defaults to `["en-GB","nl-NL"]`. It validates identifier syntax, not a language registry. Selection uses PAD's seeded generator and never changes PHP's random state.
  //
  // ## Example
  //
  // ```pad
  // {fake:locale seed=42/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/fake-locale.php`.

// Locale identifiers are selected only from the application's explicit supported set.
$locales = padFakeOpt('supported', ['en-GB', 'nl-NL']);
if (!is_array($locales) || !$locales || count($locales)>200 || array_filter($locales, fn($v)=>!is_string($v) || !preg_match('/^[A-Za-z]{2,8}(?:-[A-Za-z0-9]{1,8})*$/D',$v))) {
  if ($GLOBALS['padCheckSyntax'] ?? TRUE) padError('fake:locale supported must contain 1 to 200 locale identifiers');
  return '';
}
return padFakePick(array_values($locales));
