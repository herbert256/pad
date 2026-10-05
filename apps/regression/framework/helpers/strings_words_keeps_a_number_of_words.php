<?php

  $r = json_encode ( [
    padStrWords ( 'The quick brown fox jumps', 3 ),
    padStrWords ( 'The quick brown fox jumps', 3, ' >>>' ),
    padStrWords ( 'The quick brown', 3 ),
    padStrWords ( "  The\tquick\n\nbrown fox", 2 ),
    padStrWords ( "één\u{00A0}twee drie", 2 ),
    padStrWords ( 'a b', 0 ),
    padStrWords ( '   ', 0 ),
    padStrWords ( NULL, 2 ),
    padStrWords ( 'one two', '1' )
  ], JSON_UNESCAPED_UNICODE );

?>
