<?php

  $padTypeCheck   = $padWords [0];
  $padTypeResult  = FALSE;
  $padTypeGiven   = FALSE;
  $padTypeExplode = padExplode ($padTypeCheck, ':') ;

  if ( count ($padTypeExplode) == 1 )

    $padTypeResult = padTypeGet( $padTypeCheck );

  elseif ( count ($padTypeExplode) == 2 ) {  // ToDo: moet nog getest worden

    $padTypeGiven  = TRUE;
    $padTypeCheck  = $padTypeExplode [1];       
    $padTypeResult = padTypeCheck ( $padTypeExplode [0], $padTypeCheck ); 

  } 

  if ( $padParse and ! $padTypeResult )
    if ( $padTypeGiven ) 
      if ( padValidType ( $padTypeExplode [0] ) ) return 'runtime';
      else                                        return FALSE;
    else      
      if ( padValidTag ( $padTypeCheck ) )        return 'runtime';
      else                                        return FALSE;

  return $padTypeResult;

?>