<?php

  $padCheck = padTagParm('data');

  if ( isset ( $padDataStore [$padCheck] ) )
    return $padDataStore [$padCheck];

  return padMakeData ( $padCheck );

?>