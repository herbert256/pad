<?php

  // distinct - no value of a list twice, as written and trimmed: an item left blank is no
  // value and an item that is no text fails it; a field of one value holds it once.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleTable    = include APPS . 'regression/pages/forms/_rules.php';
  $ruleVerdicts = [];

  foreach ( [ 'distinct' ] as $ruleKey )
    foreach ( $ruleTable [$ruleKey] as [ $ruleField, $ruleList, $ruleData ] )
      $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                       . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
