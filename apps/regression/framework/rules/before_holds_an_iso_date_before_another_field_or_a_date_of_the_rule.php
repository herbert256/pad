<?php

  // before:other - an ISO day or day with a time strictly before the other field's, or before
  // a date written in the rule: the same moment is not before itself, a day is its first
  // moment, a value that is no ISO moment fails - 2026-02-30, 2026-1-5, a space for the T,
  // 24:00, a zone, a word - and an other field that is empty, absent, no moment or a list
  // lets it pass. A date written in the rule is a date, whatever field the post has of
  // that name.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleRows     = ( include APPS . 'regression/pages/forms/_rules.php' ) ['before'];
  $ruleVerdicts = [];

  foreach ( $ruleRows as [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                     . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
