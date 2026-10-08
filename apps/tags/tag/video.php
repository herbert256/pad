<?php

  $tagAbout   = 'Shows a YouTube or Vimeo video as its poster with a play button, loading the player only when pressed - or a video file as a video element.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{video 'https://www.youtube.com/watch?v=id', title='text'}
{video 'https://vimeo.com/id#t=1m30s', title='text', poster='photos/still.jpg', ratio='16:9'}
{video 'clips/sunrise.webm', title='text', poster='photos/sunrise.jpg'}
PAD;

  $tagParms   = [
    'address' => 'A YouTube address (<code>watch?v=</code>, <code>youtu.be/</code>, <code>/shorts/</code>, <code>/embed/</code>, <code>/live/</code>, youtube-nocookie.com), a Vimeo address (<code>vimeo.com/&lt;id&gt;</code>, <code>player.vimeo.com/video/&lt;id&gt;</code>) or a <code>.mp4</code>, <code>.webm</code>, <code>.ogv</code> or <code>.mov</code> file of <code>www/&lt;application&gt;/</code>.' ];

  $tagOptions = [
    'title'  => 'Required: the play button\'s text, the iframe\'s title, the video element\'s name.',
    'poster' => 'A picture of <code>www/&lt;application&gt;/</code> (versioned as <code>{asset}</code>) in place of the site\'s thumbnail.',
    'start'  => 'The second to start at - <code>90</code>, <code>1m30s</code> or <code>1:30</code>; a <code>t=</code>, <code>start=</code> or <code>#t=</code> in the address says it too. Note: <code>start</code> is also the handling option that skips occurrences, and it currently takes the video away - write the start time in the address instead.',
    'ratio'  => 'The proportion, default <code>16:9</code>; a short is <code>9:16</code> and kept to a phone\'s width.' ];

  $tagSee     = [ 'img', 'asset', 'modal' ];

?>
