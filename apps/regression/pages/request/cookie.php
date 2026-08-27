<?php

  $curl = padCurl ( [ 'url' => $padGoExt . 'request/jar&padInclude',
                      'cookies' => [ 'jar' => 'filled' ] ] );

  $cookieResult = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>