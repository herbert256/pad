<?php

  // Reads a parenthesised literal list - ( 'a', $b, 2 * 3 ) - and returns it as a PAD data
  // array. Drops the outer parentheses, splits on commas and runs every element through
  // padEval, so entries may be expressions rather than plain literals. Included by
  // padData() as data/<type>.php; padContentType picks 'list' when the text both opens
  // with ( and closes with ).

  $work = padExplode(substr($data, 1, -1), ',');

  foreach ($work as $key => $value)
    $result [$key] = padEval($value);

  return $result;

?>