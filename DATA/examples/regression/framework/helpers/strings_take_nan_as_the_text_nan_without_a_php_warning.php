<?php

  // A number is its digits as a text, and NAN - fdiv ( 0, 0 ), sqrt ( -1 ) - is NAN: PHP
  // 8.5 warns when NAN is cast to a string, and that warning ended the request in every
  // string helper handed one.

  $r = json_encode ( [
    padStrLimit ( fdiv ( 0, 0 ), 10 ),
    padStrSlug ( NAN ),
    padStrAfter ( NAN, 'A' ),
    padStrPlural ( NAN ),
    padStrIs ( 'N*', NAN )
  ] );

?>
