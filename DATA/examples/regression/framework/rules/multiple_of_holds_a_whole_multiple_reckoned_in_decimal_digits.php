<?php

  // multiple_of:n - a whole multiple of n, reckoned in the decimal digits as written and never
  // in floating point: 0.3 is a multiple of 0.1, 19.999 no multiple of 0.01, a number of
  // thirty digits judged to its last; the value a number as numeric reads one, sign, exponent
  // and all, and the field a number field, so max compares its value.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleTable    = include APPS . 'regression/pages/forms/_rules.php';
  $ruleVerdicts = [];

  foreach ( [ 'multiple_of' ] as $ruleKey )
    foreach ( $ruleTable [$ruleKey] as [ $ruleField, $ruleList, $ruleData ] )
      $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                       . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
