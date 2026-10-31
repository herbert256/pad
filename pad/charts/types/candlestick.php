<?php

  // candlestick - A candle per row (label the day): the body from open to close, the wick from
  // low to high - green when it closed at or above its open, red when lower; volume adds light
  // bars in the lower fifth on a scale of their own; the price axis needs no zero. Options:
  // label, open, high, low, close, volume; width= and height=, 600 x 300 unless given.

  return padChartCandlestick ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
