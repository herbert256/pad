<?php

  // Where the fields of a template get their values (editFields, _lib/language.php).

  $app = editApp ( editArg ( $body, 'app' ) );

  editPath ( $app, 'app', editArg ( $body, 'path' ) );

  return editFields ( $app, editArg ( $body, 'path' ) );

?>
