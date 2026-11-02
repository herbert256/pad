<?php

  // before_or_equal:other - a moment before another or the same one, as before: reads them: a
  // start no later than the end, before_or_equal:end, or before_or_equal:2026-12-31, a date
  // written in the rule. A value that is no ISO day or day with a time fails; an other field
  // that is empty or no moment lets it pass. Its argument is a field name or a date -
  // before_or_equal: with none, or before_or_equal:2026-02-30, is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data ) => padRuleWhen ( $value, $arg, $data, -1 ) or padRuleWhenSame ( $value, $arg, $data ),
    'argument' => fn ( $arg ) => padRuleWhenArgument ( $arg, 'before_or_equal' ),
    'fields'   => fn ( $arg ) => padRuleDateArg ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleDateArg ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => ':label must be a date on or before :n'
  ];

?>
