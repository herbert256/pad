<?php

  // {video 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'} - a
  // YouTube or Vimeo video as its poster with a play button, the player's iframe loaded only
  // when it is pressed, from youtube-nocookie.com or with Vimeo's dnt=1 (lib/video.php):
  // nothing of the video site runs before the visitor asks for it. Without script the poster
  // links to the video on its own site. The addresses it knows: youtube.com/watch?v=,
  // youtu.be/, /shorts/ (upright), /embed/, vimeo.com/<id>, player.vimeo.com/video/<id>.
  //
  // poster= a picture of www/<application>/ in place of the site's thumbnail; start= the
  // second to start at (a t= in the address says it too); ratio= the proportion, 16:9.
  // A file of www/<application>/ - {video 'clips/sunrise.webm', poster='photos/sunrise.jpg'}
  // - is a <video controls preload="none">. title= is required: the button's text, the
  // iframe's title, what a screen reader says.

  $padVideoTitle = trim ( (string) padTagParm ( 'title' ) );

  if ( $padVideoTitle === '' and $padCheckSyntax )
    padError ( "the video tag needs a title= - the play button's text and the player's name for a screen reader" );

  return padVideo ( $padParm, $padVideoTitle, [ 'poster' => padTagParm ( 'poster', '' ),
                                                'start'  => padTagParm ( 'start',  0  ),
                                                'ratio'  => padTagParm ( 'ratio',  '' ) ] );

?>
