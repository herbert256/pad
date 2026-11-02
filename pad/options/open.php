<?php

  // Implements open="text": prefixes the text wrapped in {property:first}...{/property:first}, so it is emitted
  // once before the first occurrence. Reached only from options/print.php.

  // A value goes in as text, a written-out option as template text - see options/close.php.

  $padContent = '{property:first}' . ( ( $padProtectValues and ! padOptionLiteral ( 'open' ) ) ? padProtect ( padTagParm ('open') ) : padTagParm ('open') ) . '{/property:first}' . $padContent;

?>
