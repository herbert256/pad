<?php

  // Implements close="text": appends the text wrapped in {last}...{/last}, so it is emitted
  // once after the final occurrence. Reached only from options/print.php.

  $padContent .= '{last}' . padTagParm ('close') . '{/last}';

?>