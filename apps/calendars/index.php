<?php

  $calendarMonth = padCalendarMonth ( $_GET ['month'] ?? '' ) ?? padNow ( 'Y-m' );
  $calendarDemo = [ [ 'when' => "$calendarMonth-04", 'what' => 'Design review' ],
                    [ 'when' => "$calendarMonth-12", 'what' => 'Team lunch' ],
                    [ 'when' => "$calendarMonth-21", 'what' => 'Release' ] ];

?>
