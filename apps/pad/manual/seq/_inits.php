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

  if ( ! isset ( $go ) or ! $go )
    $go = 'index';

  $title = 'Sequences - ' . $nows [$go] ['text']

?>