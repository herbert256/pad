<?php

  $padCheck = padTagParm('data');

  if ( isset ( $padDataStore [$padCheck] ) )
    return $padDataStore [$padCheck];

  return padData ( $padCheck );

?>