<?php

  // The labels of an a4-3x8 sheet: three across, eight down, 63.5 by 33.9 mm, 2.5 mm between the
  // columns, the grid centred - 7.25 mm from the left, 12.9 mm from the top. skip=2 leaves the
  // first two labels empty, so the first item stands in the third, at 139.25; every cell has
  // its edge, dashed and for the screen alone.

  $answer = printablesShow ( <<<'PAD'
{pad:data 'people'}
[ { "name": "Ann Smith", "street": "Main Street 1", "city": "3511 AB Utrecht", "company": "Acme", "code": "T-001" },
  { "name": "Bob Jones", "street": "High Road 22", "city": "1012 JS Amsterdam", "company": "Globex", "code": "T-002" },
  { "name": "Cleo Brown", "street": "Church Lane 3", "city": "2511 CV Den Haag", "company": "Initech", "code": "T-003" } ]
{/pad:data}
{printable:labels data='people', sheet='a4-3x8', skip=2}<b>{$name}</b><br>{$street}<br>{$city}{/printable:labels}
PAD, 9 );

?>
