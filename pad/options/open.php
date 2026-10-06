<?php

  // Implements open="text": prefixes the text wrapped in {first}...{/first}, so it is emitted
  // once before the first occurrence. Reached only from options/print.php.

  // A value goes in as text under $padProtectValues - see options/close.php.

  $padContent = '{first}' . ( $padProtectValues ? padProtect ( padTagParm ('open') ) : padTagParm ('open') ) . '{/first}' . $padContent;

?>
