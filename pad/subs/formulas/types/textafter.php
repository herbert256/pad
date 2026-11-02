<?php
  // TEXTAFTER(text, delimiter, [instance_num], [match_mode], [match_end], [if_not_found]).
  include_once PAD . 'subs/formulas/_lib/delimited.php';
  return [ 'group' => 'Text', 'args' => [ 2, 6 ],
    'call' => fn ( $args, $env ) => padFormulaDelimited ( $args, $env, TRUE ) ];
?>
