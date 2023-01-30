<?php

  if ( ! in_array ( $padTag [$pad], ['if', 'case', 'while', 'until'] )  ) {
   
    $padPrmTmp = padParseOptions ( $padPrm [$pad] [0] );
    
    foreach ( $padPrmTmp as $padV ) {

      $padW = padExplode ($padV, '=', 2);

      if ( count($padW) == 2 and substr($padW[0], 0, 1) == '$') {

        $padPrmCnt++; 

        $padSetName  = trim(substr($padW[0], 1));
        $padSetValue = $padW[1];

        if ( ! padValid ($padSetName) )
          padError ("Invalid variable name: $padSetName");

        if ( $padTag [$pad] <> 'set' or $padPair [$pad] )
          if ( isset($GLOBALS [$padSetName]) )
            $padSetSave [$pad] [$padSetName] = $GLOBALS [$padSetName];
          else
            $padSetDelete [$pad] [] = $padSetName;

        $padSetValue = padVarOpts ( '', padExplode($padSetValue, '|') );
        
        $GLOBALS [$padSetName]       = $padSetValue;
        $padPrm [$pad] [$padPrmCnt]  = $padSetValue;
        $padPrm [$pad] [$padSetName] = $padSetValue;

      } 
  
    }
 
  }

?>