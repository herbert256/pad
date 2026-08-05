<?php

  // Pipe function html: htmlspecialchars, escaping &, <, > and quotes so the value is safe
  // as HTML text. It does not hide PAD's braces from the parser - that is what ignore does.

  return htmlspecialchars ($value);

?>