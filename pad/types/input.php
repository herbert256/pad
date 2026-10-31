<?php

  // Type handler for a rich form field: {input:slider 'volume', min=0, max=100/}, or a bare tag
  // naming a file of inputs/types/ when nothing else has the name, {slider 'volume'/}. The kind
  // is the tag's name, and the field is written by inputs/input.php - run as a built-in tag's
  // PHP half is run, types/_go/tag.php. Its items are read raw, as {input}'s are (padParmsRaw,
  // lib/attrs.php): label=, rules=, min= and every attribute are the field's, never an option
  // of the engine - rows= of a markdown editor is no handling option.

  $padInputKind = $padTag [$pad];

  $padTagGo = PAD . 'inputs/input';

  return include PAD . 'types/_go/tag.php';

?>
