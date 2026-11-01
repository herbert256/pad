<?php

  // A page the editor saved, ?page/<id>: a page with no file of its own. Its template is
  // handed to the engine by the entry point (www/live/index.php) in $padPageSource, and this
  // file is its PHP - the PHP code saved with it, run as written, or its data text read as
  // PAD reads data, a record's keys the page's variables and a list the variable rows.
  // What the PHP code echoes stands before the template, as a page's echo does.

  if ( ! liveValidId ( basename ( $padPage ) ) )
    padAbort ( 404 );

  if ( is_file ( liveDir () . basename ( $padPage ) . '.php' ) )

    include liveDir () . basename ( $padPage ) . '.php';

  else

    foreach ( liveData ( (string) @file_get_contents ( liveDir () . basename ( $padPage ) . '.data' ) ) as $liveKey => $liveValue )
      if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $liveKey ) and padValidStore ( $liveKey ) )
        $GLOBALS [$liveKey] = $liveValue;

  unset ( $liveKey, $liveValue );

?>
