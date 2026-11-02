<?php

  // Different cases must resolve a built-in helper to its canonical name on disk.
  $libs = implode ( ' ', array_map ( fn ( $tag ) => basename ( (string) padTagLib ( $tag ) ) ?: '-', [ 'markdown', 'MARKDOWN', 'markdown', 'if' ] ) );

?>
