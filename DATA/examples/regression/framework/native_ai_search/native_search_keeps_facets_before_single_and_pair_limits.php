<?php

  $GLOBALS ['nativeSearchRows'] = [ [ 'id' => 1, 'title' => 'apple apple', 'kind' => 'cake' ], [ 'id' => 2, 'title' => 'apple pie', 'kind' => 'guide' ], [ 'id' => 3, 'title' => 'apple', 'kind' => 'guide' ] ];
  $options = "'nativeSearchRows', q='apple', fields='title', filters='{" . '"kind":"guide"' . "}', facets='kind', first=1";
  $pair = padUnprotect ( padUnescape ( padCode ( '{search:en ' . $options . '}{$id}{/search:en}' ) ) );
  $pairInfo = padSearchInfo ();
  $single = padUnprotect ( padUnescape ( padCode ( '{search:auto ' . $options . ", lang='en'/}" ) ) );
  $singleInfo = padSearchInfo ();
  $ok = in_array ( trim ( $pair ), [ '2', '3' ], TRUE ) && substr_count ( $single, '<li>' ) === 1
     && $pairInfo === $singleInfo && $pairInfo === [ 'total' => 2, 'facets' => [ 'kind' => [ [ 'value' => 'guide', 'count' => 2 ] ] ] ];
  $answer = $ok ? 'native search filters precede first and facets retain both matches' : 'FAIL';

?>
