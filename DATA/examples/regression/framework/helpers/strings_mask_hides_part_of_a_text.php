<?php

  $r = json_encode ( [
    padStrMask ( 'taylor@example.com', '*', 3 ),
    padStrMask ( 'taylor@example.com', '*', -15, 3 ),
    padStrMask ( '4111111111111111', '*', 4, -4 ),
    padStrMask ( '4111111111111111', '#', -4 ),
    padStrMask ( 'Crème', '•', 1, 3 ),
    padStrMask ( 'abc', '**', -100 ),
    padStrMask ( 'abc', '*', 10 ),
    padStrMask ( 'abc', '*', 1, 0 ),
    padStrMask ( '', '*', 0 ),
    padStrMask ( NULL, '*', 0 ),
    padStrMask ( 123456, '*', 2, '2' )
  ], JSON_UNESCAPED_UNICODE );

?>
