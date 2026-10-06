<?php

  // The markers of a file: a PHP file's syntax, or a page as PAD renders it - from the text
  // sent along when there is one, else from the saved file (_lib/check.php).

  global $padHost;

  [ $app, $root, $rel, $file ] = editTarget ( $body );

  $text = isset ( $body ['text'] ) && is_string ( $body ['text'] ) ? $body ['text'] : NULL;
  $ext  = editExt ( $rel );

  if ( $ext == 'php' )
    return [ 'checked' => TRUE, 'page' => '', 'markers' => editCheckPhp ( $text ?? (string) @file_get_contents ( $file ) ) ];

  if ( $root == 'app' and ( $ext == 'pad' or $ext == 'html' ) )
    return editCheckPad ( $app, $rel, $text, (string) $padHost );

  return [ 'checked' => FALSE, 'page' => '', 'markers' => [] ];

?>
