<?php

  // An envelope is a sheet of its own size - C5, 229 by 162 mm - its sender top left, a line
  // between each pair of commas of sender=, and the address in the right half of its lower part,
  // at 45% across and down; where the stamp goes is for the screen alone.

  $answer = printablesShow ( <<<'PAD'
{pad:data 'people'}
[ { "name": "Ann Smith", "street": "Main Street 1", "city": "3511 AB Utrecht", "company": "Acme", "code": "T-001" },
  { "name": "Bob Jones", "street": "High Road 22", "city": "1012 JS Amsterdam", "company": "Globex", "code": "T-002" },
  { "name": "Cleo Brown", "street": "Church Lane 3", "city": "2511 CV Den Haag", "company": "Initech", "code": "T-003" } ]
{/pad:data}
{printable:envelope data='people', first=1, paper='c5', sender='PAD, Main Street 1, 3511 AB Utrecht'}{$name}<br>{$street}<br>{$city}{/printable:envelope}
PAD, 8 );

?>
