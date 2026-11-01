<?php

  // A post of form f: its two days show what was posted for each - stay_from and stay_to - in
  // place of value=, the range of form g, which did not come back, shows value=. The page's
  // padValidate holds the last day after the first: the message stands after the frame, tied
  // to the last day's field alone.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'stay_from' => '2026-08-10', 'stay_to' => '2026-08-03' ];

  padValidate ( [ 'stay_from' => 'required|date', 'stay_to' => 'required|date|after:stay_from' ] );

?>
