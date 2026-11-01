<?php

  // gt, gte, lt and lte - more than, at least, less than, at most a number of the rule or
  // another field, measured as min and max measure: the value on a number field, the length on
  // any other, the other field alike; an other field that is empty, a list or on a number
  // field no number lets them pass.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleTable    = include APPS . 'regression/pages/forms/_rules.php';
  $ruleVerdicts = [];

  foreach ( [ 'gt', 'gte', 'lt', 'lte' ] as $ruleKey )
    foreach ( $ruleTable [$ruleKey] as [ $ruleField, $ruleList, $ruleData ] )
      $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                       . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
