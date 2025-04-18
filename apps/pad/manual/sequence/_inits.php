<?php

  $nows ['index'] ['text'] = 'Introduction';
  $nows ['index'] ['now']  = 'index';

  $nows ['concepts'] ['text'] = 'Concepts';
  $nows ['concepts'] ['now']  = 'concepts';

  $nows ['all'] ['text'] = 'All';
  $nows ['all'] ['now']  = 'all';

  $nows ['examples'] ['text'] = 'Examples';
  $nows ['examples'] ['now']  = 'examples';

  $nows ['xref'] ['text'] = 'Xref';
  $nows ['xref'] ['now']  = 'xref';

  $nows ['regression'] ['text'] = 'Regression';
  $nows ['regression'] ['now']  = 'regression';

  $nows ['generate'] ['text'] = 'Generate';
  $nows ['generate'] ['now']  = 'generate';

  $concepts ['sequences']        = 'Something that defines a sequence list';
  $concepts ['stores']           = 'A stored sequence list';
  $concepts ['actions']          = 'An operation executed on a sequence list';
  $concepts ['plays']            = '';
  $concepts ['options']          = '';
  $concepts ['chain']            = '';
  $concepts ['names']            = '';

  if ( ! isset ( $go ) or ! $go )
    $go = 'index';

  $title = 'Sequences - ' . $nows [$go] ['text']

?>