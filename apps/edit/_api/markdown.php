<?php

  // Markdown as PAD renders it (lib/markdown.php) - raw HTML in it escaped - for the
  // preview of a .md file.

  return [ 'html' => padMarkdown ( editArg ( $body, 'text' ) ) ];

?>
