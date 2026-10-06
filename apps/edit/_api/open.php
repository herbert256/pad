<?php

  // One file, as editRead gives it: the text, or the bytes of an image or other binary.

  global $editMaxText;

  [ $app, $root, $rel, $file ] = editTarget ( $body );

  return editRead ( $file, $editMaxText );

?>
