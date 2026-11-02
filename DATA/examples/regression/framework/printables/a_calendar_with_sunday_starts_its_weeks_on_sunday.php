<?php

  // With sunday the weeks start on Sunday: February 2027 - its 1st a Monday - starts a row
  // earlier, on Sunday 31 January, in five rows where the weeks from Monday need four (cal 2
  // 2027); the week number of a row is that of its Monday, ISO 8601's, 5 to 9. On A5 turned,
  // 210 by 148 mm, everything is made smaller with the paper: a margin of 8.457 mm, the month
  // in 15.5 pt. holidays='us': Washington's Birthday, the third Monday of February, the 15th.

  $answer = calendarShow ( "{printable:calendar month='2027-02', sunday, weeks, holidays='us', paper='a5', landscape/}" );

?>
