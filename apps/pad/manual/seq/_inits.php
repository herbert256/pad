<?php

  $nows ['index'] ['text'] = 'Introduction';
  $nows ['index'] ['now']  = 'index';

  $nows ['concepts'] ['text'] = 'Concepts';
  $nows ['concepts'] ['now']  = 'concepts';

  $nows ['others'] ['text'] = 'Other';
  $nows ['others'] ['now']  = 'others';

  $nows ['all'] ['text'] = 'All';
  $nows ['all'] ['now']  = 'all';

  $nows ['examples'] ['text'] = 'Examples';
  $nows ['examples'] ['now']  = 'examples';

  $nows ['xref'] ['text'] = 'Xref';
  $nows ['xref'] ['now']  = 'xref';

  $concepts ['sequences'] = 'Something that defines a sequence list';
  $concepts ['stores']    = 'A stored sequence list';
  $concepts ['actions']   = 'An operation executed on a sequence list';
  $concepts ['ones']      = 'An operation that reduces a sequence list to a single value';

  if ( ! isset ( $go ) or ! $go )
    $go = 'index';

  $title = 'Sequences - ' . $nows [$go] ['text']

?>