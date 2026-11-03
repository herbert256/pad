<?php

  // A badge: a band of colour on top - 26% of its height, 14.3 mm of 55 - with title= on it in
  // white, the name below it centred in the middle, the edges printed to cut along. a4-2x5:
  // two badges of 90 by 55 across, 5 mm apart, centred - 12.5 mm from the left, 11 from the top.

  $answer = printablesShow ( <<<'PAD'
{format:auto 'people'}
[ { "name": "Ann Smith", "street": "Main Street 1", "city": "3511 AB Utrecht", "company": "Acme", "code": "T-001" },
  { "name": "Bob Jones", "street": "High Road 22", "city": "1012 JS Amsterdam", "company": "Globex", "code": "T-002" },
  { "name": "Cleo Brown", "street": "Church Lane 3", "city": "2511 CV Den Haag", "company": "Initech", "code": "T-003" } ]
{/format:auto}
{printable:badges data='people', first=2, title='PAD Conference', color='#c0392b'}<h2>{$name}</h2>{$company}{/printable:badges}
PAD, 9 );

?>
