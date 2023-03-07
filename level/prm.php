<?php

  foreach ( $padPrmTmp as $padK => $padV ) { 

    list ($padParmName, $padParmValue ) = padSplit ('=', $padV);

    if ( padValidVar ($padParmName) ) {

      if ( padExists ( APP . "options/$padParmName.php") )
        $padOptionsApp [$pad] [] = $padParmName;

      if ( $padParmValue === '' )
        $padPrm [$pad] [$padParmName] = TRUE;
      else
        $padPrm [$pad] [$padParmName] = padEval ( $padParmValue );

      unset ( $padPrmTmp [$padK] ) ;

    }

  }

?>