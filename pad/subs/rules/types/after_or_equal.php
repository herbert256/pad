<?php

  // after_or_equal:other - a moment after another or the same one, as after: reads them: the
  // end of a trip of one day, after_or_equal:start, or after_or_equal:2026-01-01, a date
  // written in the rule. The day and its first moment are the same - 2026-01-01T00:00 is on
  // or after 2026-01-01. A value that is no ISO day or day with a time fails; an other field
  // that is empty or no moment lets it pass. Its argument is a field name or a date -
  // after_or_equal: with none, or after_or_equal:2026-13-01, is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data ) => padRuleWhen ( $value, $arg, $data, 1 ) or padRuleWhenSame ( $value, $arg, $data ),
    'argument' => fn ( $arg ) => padRuleWhenArgument ( $arg, 'after_or_equal' ),
    'fields'   => fn ( $arg ) => padRuleDateArg ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleDateArg ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => ':label must be a date on or after :n'
  ];

?>
