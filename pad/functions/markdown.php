<?php

  // Pipe function markdown: reads the value as Markdown and writes it as HTML -
  // {$post.body | markdown}, {echo $help | markdown}. padMarkdown in lib/markdown.php; raw
  // HTML in the value is escaped and a link to a javascript: URL keeps only its text, so
  // a value a visitor wrote is safe to show this way. That is also why a field whose last
  // pipe is markdown skips the sanitize chain (level/var.php): it would escape the markup
  // the pipe just made, and there is nothing left in it to escape.

  return padMarkdown ( $value );

?>
