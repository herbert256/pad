<?php

  // {data 'name'}...{/data} turns its content - JSON, XML, YAML, CSV or a list - into a
  // data set kept under that name, for a later {name} to iterate. Shared body in
  // tags/_go/store.php; the parsing itself is padData() and data/<type>.php.

  return include PAD . 'tags/_go/store.php';

?>