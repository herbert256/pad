<?php

  // {pad:content 'name'}...{/pad:content} keeps its content under that name instead of printing
  // it, for a later {content:name} or {content:name} to output. Shared body in
  // tags/_go/store.php.

  return include PAD . 'tags/_go/store.php';

?>
