<?php

  if ( padTagParm ('after') and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  if ( ! $padContent and $padOpt [$pad] [1] ) 
    return padColorsFile ( padApp . $padOpt [$pad] [1] ) ;

	$padContent = '{ignore}' . padColorsString ($padContent) . '{/ignore}';

	return TRUE;

?>