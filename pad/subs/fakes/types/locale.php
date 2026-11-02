<?php
// Locale identifiers are selected only from the application's explicit supported set.
$locales = padFakeOpt('supported', ['en-GB', 'nl-NL']);
if (!is_array($locales) || !$locales || count($locales)>200 || array_filter($locales, fn($v)=>!is_string($v) || !preg_match('/^[A-Za-z]{2,8}(?:-[A-Za-z0-9]{1,8})*$/D',$v))) {
  if ($GLOBALS['padCheckSyntax'] ?? TRUE) padError('fake:locale supported must contain 1 to 200 locale identifiers');
  return '';
}
return padFakePick(array_values($locales));
