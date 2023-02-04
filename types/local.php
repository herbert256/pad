<?php

  list ( $padLocalFile, $padLocalExt ) = padSplit ( '.', $padTag [$pad] );

  $padLocalName = padTagParm ( 'name',  $padLocalFile );

  $padName [$pad] = $padLocalName;

  return padMakeData ( $padTag [$pad], $padLocalExt, $padLocalExt, $padLocalName );
 
?>