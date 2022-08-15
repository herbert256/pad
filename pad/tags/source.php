<?php

  if ( padTagParm ('after') and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

	$padContent = '{ignore}' . padColorsString ($padContent) . '{/ignore}';

	return TRUE;

?>