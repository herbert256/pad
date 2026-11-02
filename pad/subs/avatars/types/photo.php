<?php

  // A local or HTTP(S) photograph with a generated fallback, without server-side fetching.
  return padAvatarPhoto ( padParmText ( $padParm ), padParmText ( padTagParm ( 'alt', 'Avatar' ) ),
    padWidgetNumber ( 'avatar', 'size', 64 ),
    padAvatarChoice ( 'fallback', 'initials', [ 'initials', 'identicon', 'geometric' ] ),
    padAvatarChoice ( 'shape', 'circle', [ 'circle', 'square' ] ) );

?>
