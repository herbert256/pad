<?php

  $padGetName = padTagParm ( 'data' );
  $padCheck   = padTagParm ( 'data' );

  if ( isset ( $padDataStore [$padCheck] ) )
    return $padDataStore [$padCheck];

  if ( isset ( $pqStore [$padCheck] ) )
    return $pqStore [$padCheck];

  return padData ( $padCheck );

?>