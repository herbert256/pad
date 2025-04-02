<?php

  $padHandNegative = $padPrm [$pad] ['negative'] ?? 0 ;

  foreach ( $padParms [$pad] as $padHand ) {

    extract ( $padHand );

        if ( ! count ( $padData [$pad] )                                                    ) continue;
    elseif ( $padPrmKind <> 'option'                                                        ) continue;
    elseif ( ! file_exists ( "handling/types/$padPrmName.php" )                             ) continue; 
    elseif ( $padTagSeq [$pad] and file_exists ( "sequence/actions/types/$padPrmName.php" ) ) continue; 
    elseif ( $padTagSeq [$pad] and file_exists ( "sequence/options/types/$padPrmName.php" ) ) continue; 

    $padHandName = $padPrmName;
    $padHandParm = $padPrmValue;
    $padHandCnt  = ( $padHandParm === TRUE or ! ctype_digit ( $padHandParm ) ) ? 1 : $padHandParm;

    if ( $padInfo ) 
      include 'events/handling.php'; 

    if ( $padHandNegative )
      include "handling/negative/inits.php";

    include "handling/types/$padHandName.php";

    if ( $padHandNegative )
      include "handling/negative/exits.php";

  }

?>