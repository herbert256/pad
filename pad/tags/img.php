<?php

  // {img 'photos/harbour.jpg', width=400, height=300, alt='The harbour at dawn'} - a picture
  // of www/<application>/ as a thumbnail of the size the page shows it at, made once with
  // GD and kept in www/<application>/_thumbs/ for the web server to send (lib/img.php).
  // width= and height= are the box; fit= cover (default: fill it, cut around the centre) or
  // contain (all of it inside); format= jpeg, png or webp, else the source's; quality= 1 to
  // 100 (80). The <img> carries its width and height, loading="lazy", decoding="async" and
  // a srcset with the picture at twice the size, as far as the source reaches.
  //
  // alt= is required: a picture without one is read out by its file name. alt='' says the
  // picture is decoration, and a screen reader passes it by.

  $padImgAlt = padTagParm ( 'alt', NULL );

  if ( $padImgAlt === NULL ) {
    if ( $padCheckSyntax )
      padError ( "the img tag needs an alt= text for whoever cannot see the picture - alt='' when it is decoration" );
    $padImgAlt = '';
  }

  return padImg ( $padParm, [ 'alt'     => $padImgAlt,
                              'width'   => padTagParm ( 'width',   0         ),
                              'height'  => padTagParm ( 'height',  0         ),
                              'fit'     => padTagParm ( 'fit',     'cover'   ),
                              'format'  => padTagParm ( 'format',  ''        ),
                              'quality' => padTagParm ( 'quality', 80        ) ] );

?>
