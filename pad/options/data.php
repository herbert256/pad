<?php

  $padCheck = padTagParm('data');

  if ( isset ( $padDataStore [$padCheck] ) )
    return $padDataStore [$padCheck];

 if ( isset ( $padSeqStore [$padCheck] ) )
    return $padSeqStore [$padCheck];

  return padData ( $padCheck );

?>