<?php
  $documents = [ 'Original' => 'The red bicycle waits beside the old station.', 'Edited' => 'The old red bicycle waits beside a station.', 'Unrelated' => 'A recipe for lemon cake and vanilla cream.', 'Exact copy' => 'The red bicycle waits beside the old station.' ];
  $duplicateResult = padTextAnalysis ( 'similarity', $documents, [ 'metric' => 'jaccard', 'threshold' => .5 ] );
  $duplicateRows = $duplicateResult ['pairs'];
?>
