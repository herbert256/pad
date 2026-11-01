<?php

  // likert - Answers on an ordered scale: a bar per question (label), the answers of value= in
  // their order from left to right - two to eight, the most negative first - each its share of
  // the question's total, centred on the zero line, the neutral middle of an odd count split
  // by it; a diverging scale of colours, a legend, ranked by the share of positive answers.
  // Options: label, value, ranked; width= and height=, 600 x 300 unless given.

  return padChartLikert ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
