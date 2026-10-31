<?php

  // decimal - A number from from= to to= - 0 to 1 unless said otherwise - rounded to
  // decimals= places, 2 (padFakeFloat).

  return padFakeFloat ( padFakeOpt ( 'from', 0 ), padFakeOpt ( 'to', 1 ), padFakeOpt ( 'decimals', 2 ) );

?>
