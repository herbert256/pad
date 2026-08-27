<?php

  // A real multipart upload: the payload file travels as a file field and arrives with
  // its name, its size and PHP's own word that it was uploaded.

  $curl = padCurl ( [ 'url' => $padGoExt . 'request/up&padInclude',
                      'post' => [ 'f' => new CURLFile ( APP . 'request/_payload.txt' ) ] ] );

  $uploadResult = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>