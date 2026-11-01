<?php

  // before:other - a moment strictly before another: before:end, the other field of that
  // name, or before:2027-01-01, a date written in the rule - one that reads as a date (four
  // digits, a dash, a digit) is a date, whatever field the post has of that name. Moments
  // are ISO 8601 alone, as padRuleMoment reads them: the day 2026-11-01 - its first moment
  // - or the day with a time, 2026-11-01T14:30, what date and datetime-local fields post;
  // the same moment is not before itself. A value that is no moment fails; an other field
  // that is empty or no moment lets it pass, as its own rules speak about it. Its argument
  // is a field name or a date - before: with none, or before:2026-02-30, is the author's
  // error.

  return [
    'check'    => fn ( $value, $arg, $data ) => padRuleWhen ( $value, $arg, $data, -1 ),
    'argument' => fn ( $arg ) => padRuleWhenArgument ( $arg, 'before' ),
    'shown'    => fn ( $arg ) => padRuleDateArg ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => ':label must be a date before :n'
  ];

?>
