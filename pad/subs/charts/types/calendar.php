<?php

  // calendar - A square per day, a column per week (Monday first), the day's sum of value -
  // else a count of its rows - in seven steps, an empty day in the grid colour; from the
  // Monday before the first date to the last, or the whole year. Options: date, value, year;
  // width= and height=, 720 x 150 unless given.

  return padChartCalendar ( padChartGather (), ...padChartSize ( 720, 150 ) );

?>
