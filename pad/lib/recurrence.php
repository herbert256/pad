<?php
  // Calendar occurrences only: the existing cron scheduler remains independent.
  function padOccurrences ( $kind, $rule, $from, $to ) {
    include_once PAD . 'subs/recurrences/_lib/recurrence.php';
    if ( ! padKind ( 'recurrences', $kind ) or ! is_array ( $rule ) ) throw new InvalidArgumentException ( 'Unknown recurrence or invalid rule' );
    if ( $kind === 'rrule' ) [ $kind, $rule ] = padRecurrenceRrule ( $rule );
    $type = include PAD . "subs/recurrences/types/$kind.php";
    return padRecurrenceExpand ( $kind, $type, $rule, $from, $to );
  }
?>
