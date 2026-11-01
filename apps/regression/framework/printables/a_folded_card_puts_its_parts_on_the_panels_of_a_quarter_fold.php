<?php

  // A folded card on A4, a quarter fold: the top half folded back along 148.5 mm, then the
  // sheet folded in two along 105 mm, a card of A6. Folded, the lower right quarter is the
  // front - the <header> - and the lower left the back - the <footer>; the upper half lies
  // inside, upside down on the sheet so that it reads the right way up in the card: the upper
  // right quarter - behind the front - is the inside left, the <aside>, and the upper left
  // the inside right, the rest of the item. The folds and the names of the panels are the
  // screen's alone. Letter turned: a card of 139.7 by 107.95 mm, the same quarters.

  $answer = printablesShow ( '{printable:foldcard}<header>Front</header><aside>Left</aside><p>Right</p><footer>Back</footer>{/printable:foldcard}', 10 )
          . ' ## ' . printablesShow ( "{printable:foldcard paper='letter', landscape}<header>Front</header><p>Right</p>{/printable:foldcard}", 8 );

?>
