<?php

  if ( padXref ) include pad . 'info/types/xref/items/result.php';
  if ( padXweb ) include pad . 'info/types/xweb/items/result.php';

  if ( padXref and str_contains ($padTagContent.$padContent, 'content@') )
    padXref ('constructs', 'content');  

  if ( padXweb and str_contains ($padTagContent.$padContent, 'content@') )
    padXweb ('constructs', 'content');  

  if ( padXref ) include pad . 'info/types/xref/items/flags.php';
  if ( padXweb ) include pad . 'info/types/xweb/items/flags.php';

?>