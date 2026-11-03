<?php

  include_once APPS . 'tags/_lib/kinds.php';
  $ok = 0;
  foreach ( padKinds ( 'calendars' ) as $kind ) {
    $example = tagsKindExample ( 'calendar', $kind );
    $syntax = tagsKinds () ["calendar:$kind"] ['syntax'];
    $rows = in_array ( $kind, [ 'timegrid', 'resources', 'availability' ], TRUE )
      ? [ [ 'title' => 'Review', 'start' => '2026-11-02T09:00:00Z', 'end' => '2026-11-02T10:00:00Z', 'resource' => 'room-a' ] ]
      : [ [ 'when' => '2026-11-04', 'what' => 'Review' ] ];
    $data = "{format:json 'appointments'}" . json_encode ( $rows ) . '{/format:json}';
    $exampleHtml = calendarViewOutput ( $example ['source'] );
    $syntaxHtml = calendarViewOutput ( $data . $syntax );
    $viewClass = in_array ( $kind, [ 'timegrid', 'resources' ], TRUE ) ? 'pad-timegrid' : 'pad-calendar';
    if ( $kind === 'availability' ) $viewClass = 'pad-availability';
    $content = $kind === 'availability' ? 'room-a' : 'Review';
    if ( str_contains ( $exampleHtml, $viewClass ) && str_contains ( $syntaxHtml, $viewClass ) && str_contains ( $syntaxHtml, $content ) )
      $ok++;
  }
  $answer = "$ok calendar examples and syntax samples render";

?>
