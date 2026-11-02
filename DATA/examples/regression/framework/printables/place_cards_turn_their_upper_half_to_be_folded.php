<?php

  // A place card is the same name twice, the upper half turned upside down, a dashed fold
  // between the halves: folded, it reads from both sides of the table. a4-2x2 fills the sheet
  // with four cards of 105 by 148.5, cut along the grey lines between them.

  $answer = printablesShow ( <<<'PAD'
{printable:placecards copies=1}<h2>Ann Smith</h2>{/printable:placecards}
PAD, 9 );

?>
