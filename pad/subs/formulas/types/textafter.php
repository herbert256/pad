<?php

  // textafter - TEXTAFTER(text, delimiter, [instance_num], [match_mode],
  // [match_end], [if_not_found]) returns the text after a selected delimiter.
  // A negative instance counts from the end; match_mode 1 ignores case.
  //
  //   =TEXTAFTER("a/b/c", "/")  b/c
  //   =TEXTAFTER("abc", "/", 1, 0, 0, "missing")  missing

  // TEXTAFTER(text, delimiter, [instance_num], [match_mode], [match_end], [if_not_found]).
  include_once PAD . 'subs/formulas/_lib/delimited.php';
  return [ 'group' => 'Text', 'args' => [ 2, 6 ],
    'call' => fn ( $args, $env ) => padFormulaDelimited ( $args, $env, TRUE ) ];
?>
