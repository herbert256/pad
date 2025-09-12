<?php

  $nows ['index'] ['text']   = 'Concepts';
  $nows ['index'] ['now']    = 'index';

  $nows ['sequences'] ['text']   = 'Sequences';
  $nows ['sequences'] ['now']    = 'sequences';

  $nows ['actions'] ['text']    = 'Actions';
  $nows ['actions'] ['now']     = 'actions';

  $nows ['examples'] ['text']   = 'Examples';
  $nows ['examples'] ['now']    = 'examples';

  $nows ['xref'] ['text']       = 'Xref';
  $nows ['xref'] ['now']        = 'xref';

  $nows ['regression'] ['text'] = 'Regression';
  $nows ['regression'] ['now']  = 'regression';

  if ( ! isset ( $go ) or ! $go )
    $go = 'index';

  $title = 'Sequences - ' . $nows [$go] ['text']

?>