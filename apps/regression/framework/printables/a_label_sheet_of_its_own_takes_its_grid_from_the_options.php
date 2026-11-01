<?php

  // A sheet of one's own: across=2 and down=3 labels of 80 by 40 mm, margin= 20 mm from the
  // top and 15 from the left, pitch= 90 across and 45 down - the second column at 15 + 90 =
  // 105, the second row at 20 + 45 = 65; outline prints the edges, solid. copies=2 writes
  // the one item twice.

  $answer = printablesShow ( <<<'PAD'
{printable:labels across=2, down=3, width=80, height=40, margin='20 15', pitch='90 45', outline, copies=2}Fragile{/printable:labels}
PAD, 6 );

?>
