<?php

  // What the editor knows about PAD and PHP (_lib/language.php): the part every application
  // shares when asked for it, and the application's own names.

  $app  = editArg ( $body, 'app' );
  $out  = [];

  if ( ! empty ( $body ['base'] ) )
    $out ['base'] = editLanguageBase ();

  if ( $app !== '' )
    $out ['app'] = editLanguageApp ( editApp ( $app ) );

  return $out;

?>
