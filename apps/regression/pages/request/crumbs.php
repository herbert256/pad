<?php

  $curl = padCurl ( [ 'url'     => $padGoExt . 'request/crumb&padInclude&pqTrap=1&_trap=1',
                      'cookies' => [ 'crumb' => 'baked' ] ] );

  $crumbResult = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>
