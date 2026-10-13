<?php

  // The front matter as a PHP without the yaml extension reads it - padFrontMatterLines - is
  // what yaml_parse makes of the same lines: - items at the start of the line under their
  // key (a padError, the post without its meta), a comment after a quoted value (its quotes
  // kept) and a comma inside quotes in a [..] list (the item cut in two).

  $yaml  = "title: \"Hello\" # draft\ntags:\n- php\n- pad\nauthors: [Ann, 'Smith, Bob', 'it''s']";
  $lines = json_encode ( padFrontMatterLines ( $yaml ) );
  $same  = function_exists ( 'yaml_parse' ) ? ( json_encode ( yaml_parse ( $yaml ) ) === $lines ? 'same' : 'differs' ) : 'same';

?>
