<?php

  function trimFiles ( $path ) {

    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file = $loop_info->getPathname();
      $ext  = substr($file, strrpos($file, '.')+1 );

      if ( $ext != 'php' and $ext != 'pad' )
        continue;

      // Whatever whitespace a file ends with becomes exactly one newline, the way an
      // editor saves it - a file without one gets it, an empty file stays empty.
      //
      // Read natively, as it is written: padFileGet takes a path outside the engine, the
      // applications and DATA for a DATA path, so www/, editors/, home/ and wasm/ read as
      // empty and were never trimmed.

      $old = file_get_contents ($file);
      $new = trim($old);

      if ($new !== '')
        $new .= "\n";

      if ($old != $new)
        file_put_contents ( $file, $new, LOCK_EX );

    }

  }
  
?>
