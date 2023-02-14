<?php

  $padPrmCnt = 0;
  $padWords  = preg_split("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  $padPrm [$pad]     = [];
  $padPrm [$pad] [0] = trim($padWords[1] ?? '');
  $padPrm [$pad] [1] = '';

  if ( in_array ( $padTag [$pad], $padNoParmsParse  )  )
    return;
   
  $padPrmTmp = padParseOptions ( $padPrm [$pad] [0] );
  
  foreach ( $padPrmTmp as $padV ) { 

    list ($padParmName, $padParmValue ) = padSplit ('=', $padV);

    $padPrmCnt++; 

    if ( $padParmValue !== '' and substr($padParmName, 0, 1) == '$' ) 

      include 'set.php';

    elseif ( padValidVar ($padParmName) ) {

      if ( file_exists ( APP . "options/$padParmName.php") )
        $padOptionsApp [$pad] [] = $padParmName;

      if ( $padParmValue === '' ) {
        $padPrm [$pad] [$padParmName] = TRUE;
        $padPrm [$pad] [$padPrmCnt] = $padParmName; 
      }
      else {                    
        $padPrm [$pad] [$padParmName] = padEval ( $padParmValue );
        $padPrm [$pad] [$padPrmCnt] = $padPrm [$pad] [$padParmName];
        if ( $padPrmCnt == 1 )
          $padPrm [$pad] ['_first_key_'] = $padParmName;
      }
 
    }
 
    else
 
      $padPrm [$pad] [$padPrmCnt] = padEval ( $padV );

  }

  $padPrm [$pad] ['_parms_'] = $padPrmCnt;

?>