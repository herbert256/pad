<?php

  $link = padSignedUrl ( 'invoice', [ 'id' => 1042 ], 3600 );

  $shown = preg_replace (
    [ '/padExpires=\d+/', '/padSignature=\w+/' ],
    [ 'padExpires=...',   'padSignature=...'   ],
    $link
  );

  $here  = padSignatureValid () ? 'yes' : 'no';

?>
