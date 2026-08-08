<?php

  // Pipe function like(pattern): SQL LIKE matching. The pattern is translated into a regular
  // expression, with % standing for any run of characters, _ for a single character, and
  // \% \_ \\ for those characters taken literally. The match is anchored at both ends and
  // case-insensitive, and the result is '1' or ''.

  $escape = '\\';

  $expr = '/((?:'.preg_quote($escape, '/').')?(?:'.preg_quote($escape, '/').'|%|_))/';
  $parts = preg_split($expr, $parm [0], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

  $expr = '/^';
  $lastWasPercent = FALSE;

  foreach ($parts as $part) {

      switch ($part) {
          case $escape.$escape:
              $expr .= preg_quote($escape, '/');
              break;
          case $escape.'%':
              $expr .= '%';
              break;
          case $escape.'_':
              $expr .= '_';
              break;
          case '%':
              if (!$lastWasPercent)
                  $expr .= '.*?';
              break;
          case '_':
              $expr .= '.';
              break;
          default:
              $expr .= preg_quote($part, '/');
              break;
      }

      $lastWasPercent = $part == '%';
  }

  // The u modifier makes _ and the quantifiers count characters rather than UTF-8 bytes,
  // so like('_') matches 'é'. A value that is not valid UTF-8 simply does not match.

  $expr .= '$/iu';

  return preg_match($expr, $value) ? '1' : '';

?>