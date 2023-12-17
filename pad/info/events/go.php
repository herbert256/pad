<?php

  if ( padXref )
    include pad . 'info/types/xref/items/result.php';

  if ( padXref and str_contains ($padTagContent.$padContent, 'content@') )
    padXref ('constructs', 'content');  

  if ( padXref )
    include pad . 'info/types/xref/items/flags.php';

?>