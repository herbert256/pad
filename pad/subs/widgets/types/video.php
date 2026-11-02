<?php

  // {widget:video 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'} - a
  // YouTube or Vimeo video as its poster with a play button, the player's iframe loaded only
  // when it is pressed, from youtube-nocookie.com or with Vimeo's dnt=1 (subs/embeds/_lib/video.php,
  // the addresses those of the youtube and vimeo providers of the embed subsystem, whose
  // {embed:auto} tag knows more sites): nothing of the video site runs before the visitor asks
  // for it. Without script the poster
  // links to the video on its own site. The addresses it knows: youtube.com/watch?v=,
  // youtu.be/, /shorts/ (upright), /embed/, vimeo.com/<id>, player.vimeo.com/video/<id>.
  //
  // poster= a picture of www/<application>/ in place of the site's thumbnail; at= the
  // moment to start at - 90, 1m30s or 1:30 (a t= in the address says it too); ratio= the
  // proportion, 16:9. Not start=: that is the option of every tag that starts its rows at
  // row n, and it took the video away.
  // A file of www/<application>/ - {widget:video 'clips/sunrise.webm', poster='photos/sunrise.jpg'}
  // - is a <video controls preload="none">. title= is required: the button's text, the
  // iframe's title, what a screen reader says.

  include_once PAD . 'subs/embeds/_lib/video.php';

  $padVideoTitle = trim ( padParmText ( padTagParm ( 'title' ) ) );

  if ( $padVideoTitle === '' and $padCheckSyntax )
    padError ( "the video tag needs a title= - the play button's text and the player's name for a screen reader" );

  return padVideo ( $padParm, $padVideoTitle, [ 'poster' => padTagParm ( 'poster', '' ),
                                                'start'  => padTagParm ( 'at',     '' ),
                                                'ratio'  => padTagParm ( 'ratio',  '' ) ] );

?>
