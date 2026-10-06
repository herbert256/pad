<?php

  // Implements close="text": appends the text wrapped in {last}...{/last}, so it is emitted
  // once after the final occurrence. Reached only from options/print.php.

  // The text is a value - glue=$sep may hold what a visitor wrote - and goes into the
  // level's template, so under $padProtectValues it goes in as text: a value of {echo 6 * 7}
  // ran there and printed 42. The same in open, glue and quote.

  $padContent .= '{last}' . ( $padProtectValues ? padProtect ( padTagParm ('close') ) : padTagParm ('close') ) . '{/last}';

?>
