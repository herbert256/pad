<?php

  $r = json_encode ( [
    padStrExcerpt ( 'This is my name', 'my', 3 ),
    padStrExcerpt ( 'This is my name', 'MY', 100 ),
    padStrExcerpt ( 'This is my name', 'name', 3, '(...)' ),
    padStrExcerpt ( 'This is my name', 'this', 0 ),
    padStrExcerpt ( 'This is my name', 'xyz', 3 ),
    padStrExcerpt ( 'This is my name', '', 4 ),
    padStrExcerpt ( 'Één brûlée twee', 'BRÛLÉE', 3 ),
    padStrExcerpt ( "first line\nsecond line\nthird", 'second', 5 ),
    padStrExcerpt ( 'price: $5 (each)', '$5 (', 2 ),
    padStrExcerpt ( NULL, 'x' ),
    padStrExcerpt ( 'abc', NULL, 1 )
  ], JSON_UNESCAPED_UNICODE );

?>
