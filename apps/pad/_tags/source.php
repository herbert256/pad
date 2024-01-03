<?php

  if ( padTagParm ('after') and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  if ( ! $padContent and $padParm ) 
    $padReturn = padColorsFile ( padApp . $padParm ) ;
  else
    $padReturn = padColorsString ( $padContent ) ;

	return str_replace ( '}', '&close;', $padReturn );


?>