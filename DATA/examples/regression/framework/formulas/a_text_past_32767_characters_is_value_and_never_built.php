<?php

  // A text holds 32767 characters at most, as a cell of Excel does, and a text longer than
  // that is #VALUE! - and is never built (_lib/formulas.php): SUBSTITUTE works out the length
  // it would give before it replaces, & before it joins, CONCAT and TEXTJOIN as they join,
  // TEXT as it writes an @ again and again, and any function answering a longer text answers
  // #VALUE!. Eight SUBSTITUTEs in each other, 241 characters a visitor may post into a cell,
  // built a text of 200 million characters, and nine asked for 2 GB; 20000 characters is
  // still a text.

  $many = fn ( $levels ) => array_reduce ( range ( 1, $levels ), fn ( $text ) => "SUBSTITUTE($text,\"a\",\"aaaaaaaaaa\")", '"aa"' );

  $cases = formulasRun ( [
    '=LEN(' . $many ( 4 ) . ')',
    '=LEN(' . $many ( 5 ) . ')',
    '=LEN(' . $many ( 8 ) . ')',
    '=LEN(SUBSTITUTE(' . $many ( 4 ) . ',"a","b",3))',
    '=LEN(' . $many ( 4 ) . '&' . $many ( 4 ) . ')',
    '=LEN(' . $many ( 4 ) . '&"x")',
    '=LEN(CONCAT(' . $many ( 4 ) . ',' . $many ( 4 ) . '))',
    '=LEN(TEXTJOIN(",",TRUE,' . $many ( 4 ) . ',' . $many ( 4 ) . '))',
    '=LEN(TEXT(' . $many ( 4 ) . ',"@@"))',
    '=LEN(TEXT(' . $many ( 4 ) . ',"@"))',
    '=LEN(UPPER(' . $many ( 4 ) . '))'
  ] );

  // The formulas are long: each is shown by its length and what it answers.

  $cases = preg_replace_callback ( '/=LEN\([^|]*? = /', fn ( $match ) => 'a formula of ' . ( strlen ( $match [0] ) - 3 ) . ' characters = ', $cases );

?>
