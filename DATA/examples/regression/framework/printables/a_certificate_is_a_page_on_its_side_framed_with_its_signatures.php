<?php

  // A certificate: A4 on its side, 297 by 210, framed twice in color=, the title in the frame's
  // colour, the content in the middle, set in a serif, and a line to sign on for each name of
  // signed= - a name, and its role after the comma, under it.

  $answer = printablesShow ( <<<'PAD'
{printable:certificate title='Certificate of Merit', color='#7a1f1f', signed='Jane Doe, Director; Sam Lee'}<p>Awarded to</p><h1>Ann Smith</h1>{/printable:certificate}
PAD, 9 );

?>
