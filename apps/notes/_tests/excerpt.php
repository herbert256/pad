<?php

  // An excerpt is the text without its Markdown, cut at a word with an ellipsis.

  $short = notesExcerpt ( "## Title\n\nSome **bold** and `code` - [a link](https://example.org)." );
  $long  = notesExcerpt ( str_repeat ( 'word ', 60 ) );

?>
