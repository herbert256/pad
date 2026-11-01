<?php

  // The sounds are the files of pad/subs/sounds/types/, as the engine reads them (padSoundKinds).

  $tagAbout   = 'A short sound made from a recipe and played by the browser - ' . count ( padSoundKinds () ) . ' of them, from a chime and a pop to a coin and a drum kit - a button, a binding to elements of the page, or a WAV file.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{sound 'name'/}
{sound 'pop', button='Pop!', volume=60/}
{sound:chime/}
{sound:success on='click', target='#save'/}
{sound:bell download/}
PAD;

  $tagParms   = [
    'name' => 'The sound, a file of <code>pad/subs/sounds/types/</code>: '
            . implode ( ', ', array_map ( fn ( $sound ) => "<code>$sound</code>", padSoundKinds () ) )
            . '. The sound: type names it as well - <code>{sound:chime/}</code>; it is never reached bare.' ];

  $tagOptions = [
    'button'   => 'The label of the button that plays the sound, else the title of its recipe. With <code>target=</code> a button is written only when <code>button=</code> asks for one.',
    'target'   => 'A CSS selector: no button, the sound plays when an element it finds has the event of <code>on=</code> - the elements a fragment brings later too.',
    'on'       => '<code>click</code> (default), <code>pointerdown</code>, <code>hover</code>, <code>focus</code>, <code>change</code>, <code>input</code> or <code>submit</code>.',
    'volume'   => 'A whole number 0 to 100: the share of the sound\'s own level it plays at.',
    'download' => 'Bare option: a link to the sound as a WAV file - the page asked again with <code>&amp;padSound=</code>; <code>download=\'text\'</code> its text.' ];

  $tagSee     = [ 'toast', 'toy', 'embed' ];

?>
