<?php

  $nows ['index'] ['text']      = 'Concepts';
  $nows ['index'] ['now']       = 'index';

  $nows ['sequences'] ['text']  = 'Sequences';
  $nows ['sequences'] ['now']   = 'sequences';

  $nows ['actions'] ['text']    = 'Actions';
  $nows ['actions'] ['now']     = 'actions';

  $nows ['examples'] ['text']   = 'Examples';
  $nows ['examples'] ['now']    = 'examples';

  $nows ['reference'] ['text']  = 'Reference';
  $nows ['reference'] ['now']   = 'reference';

  // The regression suite for this subsystem lives with the other framework suites, in the
  // regression application under regression/sequence.

  $title = 'Sequences';

  if ( isset ( $nows [$padPage] ) )
    $sequenceTitile = $nows [$padPage] ['text'];
  else
    $sequenceTitile = $padPage;

?>