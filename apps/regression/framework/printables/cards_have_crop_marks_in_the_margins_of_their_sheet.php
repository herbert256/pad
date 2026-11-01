<?php

  // Business cards, 85 by 55 mm, two across and five down on A4 with no room between them - 20
  // mm from the left, 11 from the top: crop marks in the margin in line with every edge, 1.5
  // mm off the cards and as long as the margin allows - 5 mm above, where the top margin is
  // 11 mm. copies=10 fills the sheet with one card.

  $answer = printablesShow ( <<<'PAD'
{printable:cards copies=10}<h3>Ann Smith</h3>Developer{/printable:cards}
PAD, 7 );

?>
