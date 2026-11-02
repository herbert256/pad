<?php

  // The fixture of format/a_page_answers_its_chapters_as_the_track_of_a_video: a talk's
  // chapters, each a start in minutes and seconds and a title, the end of each the start of
  // the next - and the last its own. Rendered it is a list; asked for vtt it is the src of a
  // <track kind="chapters">, for srt a subtitle file.

  $chapters = [
    [ 'start' => '0:00', 'title' => 'Welcome' ],
    [ 'start' => '2:30', 'title' => 'Formats & cues' ],
    [ 'start' => '11:05.5', 'end' => '15:00', 'title' => 'Questions' ]
  ];

  $padExpose = [ 'chapters' ];

?>
