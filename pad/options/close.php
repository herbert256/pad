<?php

  // Implements close="text": appends the text wrapped in {last}...{/last}, so it is emitted
  // once after the final occurrence. Reached only from options/print.php.

  // The text goes into the level's template. A value - close=$end may hold what a visitor
  // wrote - goes in as text under $padProtectValues: a value of {echo 6 * 7} ran there and
  // printed 42. A written-out option is the author's template text (padOptionLiteral), as it
  // always was, so a closing pipe sees its characters. The same in open, glue and quote.

  $padContent .= '{last}' . ( ( $padProtectValues and ! padOptionLiteral ( 'close' ) ) ? padProtect ( padTagParm ('close') ) : padTagParm ('close') ) . '{/last}';

?>
