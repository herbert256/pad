<?php

  // Responsive picture with generated width variants and matching media sources.
  $padImageOptions = padImageOptions ( 640 );
  $padImageOptions ['_picture'] = TRUE;
  $padImageOptions ['widths'] = padImageWidths ( padTagParm ( 'widths', '320,640,960,1280' ) );
  $padImageOptions ['sizes'] = padParmText ( padTagParm ( 'sizes', '(max-width: 640px) 100vw, 640px' ) );
  return padImg ( $padParm, $padImageOptions );

?>
