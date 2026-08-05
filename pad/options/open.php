<?php

  // Implements open="text": prefixes the text wrapped in {first}...{/first}, so it is emitted
  // once before the first occurrence. Reached only from options/print.php.

  $padContent = '{first}' . padTagParm ('open') . '{/first}' . $padContent;

?>