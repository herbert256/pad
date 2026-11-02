<?php

  // April 2027 - its first day a Thursday, as cal 4 2027 has it - on A4: the month, the
  // weekdays from Monday, the ISO 8601 week numbers of its five rows, 13 to 17 (Python's
  // isocalendar: 2027-03-29, the Monday of the first row, is in week 13), the 1st in the
  // fourth column, 96.286 mm from the left, the three days of March before it and the two of
  // May after it filled grey, 8 lines down and 6 across; with holidays='nl' King's Day, Tuesday
  // 27 April, red with its name, and the Sundays red. May 2027: Liberation Day an observance
  // in 2027 - a public holiday every fifth year - grey, Ascension Day on 6 May, Whit Sunday and
  // Whit Monday on 16 and 17 May (Easter on 28 March) red; six rows.

  $answer = calendarShow ( "{printable:calendar month='2027-04', months=2, holidays='nl', weeks/}" );

?>
