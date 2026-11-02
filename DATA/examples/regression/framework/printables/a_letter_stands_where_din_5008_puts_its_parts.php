<?php

  // A letter, laid out as DIN 5008 form B: the fold marks at 105 and 210 mm and the hole mark at
  // 148.5, the window of the envelope on the screen, the letterhead from the <header>, the return
  // line under 45 mm, the <address> in the field from 62.7 mm, the <aside> at 125 mm across and 50
  // down, the body from 98.46 and the <footer> at the foot.

  $answer = printablesShow ( <<<'PAD'
{printable:letter sender='PAD, Main Street 1, 3511 AB Utrecht'}<header>PAD</header><address>Ann Smith<br>Main Street 1</address><aside>1 November 2026</aside><p>Dear Ann,</p><footer>IBAN NL91</footer>{/printable:letter}
PAD, 12 );

?>
