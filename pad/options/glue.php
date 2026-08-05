<?php

  // Implements glue="text": appends the separator wrapped in {notLast}...{/notLast}, so it is
  // emitted between occurrences but not after the last one. Reached only from options/print.php.

  $padContent .= '{notLast}' . padTagParm ('glue') . '{/notLast}';

?>