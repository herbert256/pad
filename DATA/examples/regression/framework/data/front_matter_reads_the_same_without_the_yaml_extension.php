<?php

  $yaml = "title: \"The sequel: more\"\ndate: 2026-10-07\ntags: [pad, cms]\ncount: 3\ndraft: false\nlist:\n  - one\n  - 'two'\nnote: it's # a comment";

  $lines = json_encode ( padFrontMatterLines ( $yaml ) );
  $same  = function_exists ( 'yaml_parse' ) ? ( json_encode ( yaml_parse ( $yaml ) ) === $lines ? 'same' : 'differs' ) : 'same';

?>
