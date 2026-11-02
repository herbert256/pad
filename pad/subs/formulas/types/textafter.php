<?php

  // textafter - TEXTAFTER(text, delimiter, [instance], [match_mode], [match_end], [if_not_found])
  // extracts text after a literal delimiter. Negative instance counts from the end;
  // missing delimiters return if_not_found, or #N/A when that argument is omitted.
  //   =TEXTAFTER("Ada Lovelace", " ")       Lovelace
  //   =TEXTAFTER("a/b/c", "/", -1)          c
  include_once PAD . 'subs/formulas/_lib/text_extract.php';
  return [ 'group' => 'Text', 'args' => [ 2, 6 ],
    'call' => fn ( $args, $env ) => padFormulaTextExtract ( $args, $env, TRUE ) ];

?>
