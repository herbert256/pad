<?php

  // after:other - a moment strictly after another, read as before: reads it: after:start,
  // the other field of that name, or after:2026-01-01, a date written in the rule. A value
  // that is no ISO day or day with a time fails; an other field that is empty or no moment
  // lets it pass. Its argument is a field name or a date - after: with none, or
  // after:2026-13-01, is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data ) => padRuleWhen ( $value, $arg, $data, 1 ),
    'argument' => fn ( $arg ) => padRuleWhenArgument ( $arg, 'after' ),
    'fields'   => fn ( $arg ) => padRuleDateArg ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleDateArg ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => ':label must be a date after :n'
  ];

?>
