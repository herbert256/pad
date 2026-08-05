<?php

  // Pipe function like(pattern): SQL LIKE matching. The pattern is translated into a regular
  // expression, with % standing for any run of characters, _ for a single character, and
  // \% \_ \\ for those characters taken literally. The match is anchored at both ends and
  // case-insensitive, and the result is '1' or ''. Note that it reads $parm as a plain
  // string while the pipe machinery always passes an array of arguments.

  $escape = '\\';

  $expr = '/((?:'.preg_quote($escape, '/').')?(?:'.preg_quote($escape, '/').'|%|_))/';
  $parts = preg_split($expr, $parm, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

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

  $expr .= '$/i';

  return preg_match($expr, $value) ? '1' : '';

?>