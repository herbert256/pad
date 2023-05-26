<?php

  if ( padTagParm ('after') and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  if ( ! $padContent and $padOpt [$pad] [1] ) 
    $padReturn = padColorsFile ( padApp . $padOpt [$pad] [1] ) ;
  else
    $padReturn = padColorsString ( $padContent ) ;

	return str_replace ( '}', '&close', $padReturn );


?>