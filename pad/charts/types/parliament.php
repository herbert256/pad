<?php

  // parliament - A dot per seat in rows of half circles, the parties filling wedges from left
  // to right in the order of the rows; the total and the seats a majority needs in the middle,
  // a legend with the seats; whole seats, 5000 at most. Options: label, value; width= and
  // height=, 480 x 280 unless given.

  return padChartParliament ( padChartGather (), ...padChartSize ( 480, 280 ) );

?>
