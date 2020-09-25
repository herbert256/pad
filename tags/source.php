<?php

  if ( pad_tag_parm ('after') and $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

	$pad_content = '{ignore}' . pad_colors_string ($pad_content) . '{/ignore}';

	return TRUE;

?>