<?php

  include_once PAD . 'subs/images/avatars/_lib/avatar.php';
  include_once PAD . 'subs/images/avatars/_lib/identicon.php';
  include_once PAD . 'subs/images/avatars/_lib/gravatar.php';
  include_once PAD . 'subs/images/avatars/_lib/views.php';

  if ( ! padKind ( 'images/avatars', $padAvatarKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no avatar type named '" . padMakeSafe ( $padAvatarKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'images/avatars' ) ) );
    return FALSE;
  }
  if ( ! is_scalar ( $padParm ) and $padParm !== NULL ) {
    if ( $padCheckSyntax )
      padError ( 'an avatar takes one text value, not a list' );
    return FALSE;
  }
  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'avatar', $padAvatarKind );
  return include PAD . "subs/images/avatars/types/$padAvatarKind.php";

?>
