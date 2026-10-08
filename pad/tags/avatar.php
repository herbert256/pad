<?php

  // {avatar 'Herbert Jebbink'} - an initials avatar as inline SVG, drawn by lib/avatar.php.
  // The first parameter is the name: its first and last word give the letters, a hash of it
  // the colour, so the same name looks the same everywhere. size= is the width and height in
  // pixels (default 48), shape= circle (default) or square - with rounded corners - and
  // label= the accessible name, else the name itself.
  //
  //   {avatar 'Herbert Jebbink', size=48, shape='circle'}
  //   {users}{avatar $name, size=32} {$name}{/users}

  $padAvatarName  = trim ( (string) $padParm );
  $padAvatarShape = strtolower ( trim ( (string) padTagParm ( 'shape', 'circle' ) ) );

  if ( $padAvatarName === '' and $padCheckSyntax )
    padError ( 'the avatar has no name - {avatar \'Herbert Jebbink\'}' );

  if ( ! in_array ( $padAvatarShape, [ 'circle', 'square' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the avatar has no shape '" . padMakeSafe ( $padAvatarShape, 20 ) . "' - circle or square" );
    $padAvatarShape = 'circle';
  }

  return padAvatar ( $padAvatarName, (int) padTagParm ( 'size', 48 ), $padAvatarShape, (string) padTagParm ( 'label' ) );

?>
