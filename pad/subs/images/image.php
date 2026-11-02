<?php

  include_once PAD . 'subs/images/_lib/img.php';
  include_once PAD . 'subs/images/_lib/views.php';
  if ( ! padKind ( 'images', $padImageKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no image type named '" . padMakeSafe ( $padImageKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'images' ) ) );
    return FALSE;
  }
  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'image', $padImageKind );
  if ( padFileExists ( PAD . "subs/images/_lib/$padImageKind.php" ) )
    include_once PAD . "subs/images/_lib/$padImageKind.php";
  return include PAD . "subs/images/types/$padImageKind.php";

?>
