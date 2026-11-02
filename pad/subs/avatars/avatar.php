<?php

  include_once PAD . 'subs/avatars/_lib/avatar.php';
  include_once PAD . 'subs/avatars/_lib/identicon.php';
  include_once PAD . 'subs/avatars/_lib/gravatar.php';
  include_once PAD . 'subs/avatars/_lib/views.php';

  if ( ! padKind ( 'avatars', $padAvatarKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no avatar type named '" . padMakeSafe ( $padAvatarKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'avatars' ) ) );
    return FALSE;
  }
  if ( ! is_scalar ( $padParm ) and $padParm !== NULL ) {
    if ( $padCheckSyntax )
      padError ( 'an avatar takes one text value, not a list' );
    return FALSE;
  }
  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'avatar', $padAvatarKind );
  return include PAD . "subs/avatars/types/$padAvatarKind.php";

?>
