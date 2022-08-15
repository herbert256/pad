<?php

  if ( pTag_parm ('after') and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

	$padContent = '{ignore}' . pColors_string ($padContent) . '{/ignore}';

	return TRUE;

?>