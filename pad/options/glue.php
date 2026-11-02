<?php

  // Implements glue="text": appends the separator wrapped in {property:notLast}...{/property:notLast}, so it is
  // emitted between occurrences but not after the last one. Reached only from options/print.php.

  // A value goes in as text, a written-out option as template text - see options/close.php.

  $padContent .= '{property:notLast}' . ( ( $padProtectValues and ! padOptionLiteral ( 'glue' ) ) ? padProtect ( padTagParm ('glue') ) : padTagParm ('glue') ) . '{/property:notLast}';

?>
