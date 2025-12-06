<?php

  $padHandNegative = $padPrm [$pad] ['negative'] ?? 0 ;

  foreach ( $padParms [$pad] as $padHand ) {

    extract ( $padHand );

        if ( ! count ( $padData [$pad] )                        ) continue;
    elseif ( $padTagSeq [$pad]                                  ) continue; 
    elseif ( $padPrmKind <> 'option'                            ) continue;
    elseif ( ! file_exists ( PAD . "handling/types/$padPrmName.php" ) ) continue; 

    $padHandName = $padPrmName;
    $padHandParm = $padPrmValue;
    $padHandCnt  = ( $padHandParm === TRUE or ! ctype_digit ( $padHandParm ) ) ? 1 : $padHandParm;

    if ( $padInfo ) 
      include PAD . 'events/handling.php'; 

    if ( $padHandNegative )
      include PAD . "handling/negative/inits.php";

    include PAD . "handling/types/$padHandName.php";

    if ( $padHandNegative )
      include PAD . "handling/negative/exits.php";

  }

?>