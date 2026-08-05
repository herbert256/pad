<?php

  // Splits the raw text of a tag ($padBetween, what stands between { and }) into its name
  // and its options.
  //
  // $padFirst is the leading character - how variables, comments and closing tags are
  // spotted; $padTagCheck is the tag name, $padTagOpts the rest of the line and
  // $padPrmParse that rest split into comma-separated items by padParseOptions(). Re-run
  // every time $padBetween is replaced, by level/tag.php, level/pair.php and
  // level/close.php.

  $padFirst    = substr ( $padBetween , 0, 1 );
  $padWords    = preg_split ( "/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY );
  $padTagCheck = $padWords [0] ?? '';
  $padTagOpts  = $padWords [1] ?? '';
  $padPrmParse = padParseOptions ( $padTagOpts );

?>