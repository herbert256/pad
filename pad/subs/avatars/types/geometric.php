<?php

  // Geometric portraits from a stable hash, in rings, mosaic or beam styles.
  if ( trim ( padParmText ( $padParm ) ) === '' and $padCheckSyntax )
    padError ( 'the geometric avatar needs a value' );
  return padAvatarGeometric ( padParmText ( $padParm ), padWidgetNumber ( 'avatar', 'size', 64 ),
    padAvatarChoice ( 'style', 'rings', [ 'rings', 'mosaic', 'beam' ] ), padParmText ( padTagParm ( 'label' ) ) );

?>
