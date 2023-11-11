<?php
  
  $padXmlParts = padExplode ( $padTag [$pad], '-' );

  $padXmlParms = [];

  $padXmlParms ['type'] = $padXmlParts [1];

  if ( count ( $padXmlParts ) >= 3 )
    $padXmlParms ['sub-type'] = $padXmlParts [2];

  if ( count ( $padXmlParts ) >= 4 )
    $padXmlParms ['page'] = $padXmlParts [3];

  return $padXmlParms;

?>