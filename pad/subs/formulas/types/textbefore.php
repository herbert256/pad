<?php

  // textbefore - TEXTBEFORE(text, delimiter, [instance], [match_mode], [match_end], [if_not_found])
  // extracts text before a literal delimiter. Negative instance counts from the end;
  // match_mode=1 ignores case, match_end=1 also matches the boundary of the text.
  //   =TEXTBEFORE("Ada Lovelace", " ")       Ada
  //   =TEXTBEFORE("a/b/c", "/", -1)         a/b
  include_once PAD . 'subs/formulas/_lib/text_extract.php';
  return [ 'group' => 'Text', 'args' => [ 2, 6 ],
    'call' => fn ( $args, $env ) => padFormulaTextExtract ( $args, $env, FALSE ) ];

?>
