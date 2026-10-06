<?php

  // Implements glue="text": appends the separator wrapped in {notLast}...{/notLast}, so it is
  // emitted between occurrences but not after the last one. Reached only from options/print.php.

  // A value goes in as text under $padProtectValues - see options/close.php.

  $padContent .= '{notLast}' . ( $padProtectValues ? padProtect ( padTagParm ('glue') ) : padTagParm ('glue') ) . '{/notLast}';

?>
