<?php

  // {data 'name'}...{/data} turns its content - JSON, XML, YAML, CSV, a list, or a format of
  // the formats subsystem, format='toml' (lib/format.php) - into a data set kept under that
  // name, for a later {name} to iterate. Shared body in tags/_go/store.php; the parsing
  // itself is padData() and data/<type>.php or the format's reader.

  return include PAD . 'tags/_go/store.php';

?>
