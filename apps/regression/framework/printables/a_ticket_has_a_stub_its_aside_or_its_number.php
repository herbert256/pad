<?php

  // A ticket: the main part, then the stub - 28% of 190 mm, 53.2 - behind a dashed perforation
  // at 10 + 136.8 = 146.8 mm. The stub is the ticket's <aside> - the first ticket's - or, for the
  // second, which has none, its number from number=: No. 0042.

  $answer = printablesShow ( <<<'PAD'
{data 'seats'}
[ { "row": "A", "seat": 4, "stub": 1 }, { "row": "B", "seat": 7, "stub": 0 } ]
{/data}
{printable:tickets data='seats', number=41}<h2>Hamlet</h2>Row {$row}, seat {$seat}{if $stub}<aside>{$row}{$seat}</aside>{/if}{/printable:tickets}
PAD, 8 );

?>
