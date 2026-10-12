<?php

  // A link that holds a picture alone - a linked logo - is the picture in a {pdf}, the link
  // over it, where the picture was left out and its alt text stood there as a link.

  $pdf = padPdf ( '<p><a href="https://harbour.example/"><img src="assets/photo.jpg" width="56" height="56" alt="Harbour Supplies"></a></p>',
                  [ 'compress' => FALSE ] );

  preg_match_all ( '#/URI \(([^)]*)\)#', $pdf, $links );

  $drawn = 'pictures: ' . substr_count ( $pdf, '/Subtype /Image' ) . ', links: ' . implode ( ' ', $links [1] )
         . ', alt text: ' . ( str_contains ( $pdf, '(Harbour) Tj' ) ? 'written' : 'not written' );

?>
