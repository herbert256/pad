<?php

  // A page's own $holiday is read inside the days of a {calendar} pair without holidays=:
  // the days get a field holiday only when holidays= asks for the holidays, and then every
  // day has it - the name, or '' - so the page's value never stands in for a day without.

  $holiday = 'PAGEVAR';

?>
