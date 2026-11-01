<?php

  // venn - Two or three sets as circles, each one's area its size, the overlaps matched (two
  // exactly, three pair by pair); a row names a set or an overlap in sets ('A', 'A&B' or 'A,
  // B'), and a set's size counts its overlaps; light fills, names outside, the count of each
  // region where it has room. Options: sets, value; width= and height=, 480 x 280 unless
  // given.

  return padChartVenn ( padChartGather (), ...padChartSize ( 480, 280 ) );

?>
