<?php

  // min_words and max_words - at least, at most n words, the runs of characters between
  // Unicode's white space: line breaks, tabs, the no-break space and the ideographic space
  // part words, a hyphen, a byte order mark and a zero width space do not; an empty field is
  // judged by required alone.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleTable    = include APPS . 'regression/pages/forms/_rules.php';
  $ruleVerdicts = [];

  foreach ( [ 'min_words', 'max_words' ] as $ruleKey )
    foreach ( $ruleTable [$ruleKey] as [ $ruleField, $ruleList, $ruleData ] )
      $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                       . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
