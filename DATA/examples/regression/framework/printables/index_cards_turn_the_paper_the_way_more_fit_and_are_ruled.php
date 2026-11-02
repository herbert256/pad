<?php

  // Index cards of 5 by 3 inches, 127 by 76.2 mm: one across and three down fit an upright A4,
  // two across and two down a turned one - so the sheet is turned, 297 by 210, the four cards
  // centred, with crop marks. A card is ruled: a red line 13 mm down and blue ones every 6.35
  // mm under it, nine of them in 76.2 mm.

  $answer = printablesShow ( <<<'PAD'
{printable:indexcards copies=4}<h3>Word</h3>Its meaning{/printable:indexcards}
PAD, 14 );

?>
