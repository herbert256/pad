<?php

  include_once APPS . 'tags/_lib/kinds.php';
  $ok = 0;
  foreach ( padKinds ( 'calendars' ) as $kind ) {
    $example = tagsKindExample ( 'calendar', $kind );
    $syntax = tagsKinds () ["calendar:$kind"] ['syntax'];
    $data = "{pad:data 'appointments'}[{\"when\":\"2026-11-04\",\"what\":\"Review\"}]{/pad:data}";
    if ( trim ( calendarViewOutput ( $example ['source'] ) ) !== '' && trim ( calendarViewOutput ( $data . $syntax ) ) !== '' )
      $ok++;
  }
  $answer = "$ok calendar examples and syntax samples render";

?>
