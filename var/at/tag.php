<?php

  list ( $tag, $parm ) = padSplit ( ':', $names[0] );

  if ( padExists ( pad . "tag/$tag.php" ))
    return include pad . 'var/tag/tag.php';
  else
    return include pad . 'var/tag/search.php';

?>