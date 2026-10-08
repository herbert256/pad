<?php

  $visitor = padLiveEvent () == 'greet' ? trim ( (string) padRequest ( 'visitor', '' ) ) : '';

?>
