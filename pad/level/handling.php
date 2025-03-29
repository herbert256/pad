<?php

  foreach ( $padParms [$pad] as $padHand ) {

    extract ( $padHand );

    if     ( $padPrmKind <> 'option'                                                        ) continue;
    elseif ( ! file_exists ( "handling/$padPrmName.php" )                                   ) continue; 
    elseif ( $padTagSeq [$pad] and file_exists ( "sequence/actions/types/$padPrmName.php" ) ) continue; 
    elseif ( $padTagSeq [$pad] and file_exists ( "sequence/options/types/$padPrmName.php" ) ) continue; 

    $padHandName = $padPrmName;
    $padHandParm = $padPrmValue;
    $padHandCnt  = ( $padHandParm === TRUE or ! ctype_digit ( $padHandParm ) ) ? 1 : $padHandParm;

    if ( $padInfo ) 
      include 'events/handling.php'; 

    if ( count ( $padData [$pad] ) )
      include "handling/$padHandName.php";

  }

?>