<?php

  // after_or_equal and before_or_equal - after or before as those read the moments, and the
  // same moment too: an end on the day of the start, a day and its first moment, a time with
  // and without its seconds; a value that is no moment fails, an other field that is empty or
  // no moment lets it pass.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleTable    = include APPS . 'regression/pages/forms/_rules.php';
  $ruleVerdicts = [];

  foreach ( [ 'after_or_equal', 'before_or_equal' ] as $ruleKey )
    foreach ( $ruleTable [$ruleKey] as [ $ruleField, $ruleList, $ruleData ] )
      $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                       . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
