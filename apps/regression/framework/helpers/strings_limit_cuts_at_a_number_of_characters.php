<?php

  $r = json_encode ( [
    padStrLimit ( 'The quick brown fox', 10 ),
    padStrLimit ( 'The quick brown fox', 9 ),
    padStrLimit ( 'The quick', 9 ),
    padStrLimit ( 'Crème brûlée', 5, '…' ),
    padStrLimit ( 'Crème brûlée', 6, ' (more)' ),
    padStrLimit ( 'abc', 0 ),
    padStrLimit ( '', 0 ),
    padStrLimit ( NULL, 5 ),
    padStrLimit ( '0', 1 ),
    padStrLimit ( 1234567, 3 ),
    padStrLimit ( 'abcdef', '3' ),
    padStrLimit ( str_repeat ( 'x', 120 ) ) === str_repeat ( 'x', 100 ) . '...'
  ], JSON_UNESCAPED_UNICODE );

?>
