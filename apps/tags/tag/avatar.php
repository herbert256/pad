<?php

  $tagAbout   = 'Draws an initials avatar as inline SVG - the letters of a name in a circle coloured by a hash of it, no image needed.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{avatar 'name'}
{avatar $name, size=48, shape='square', label='text'}
PAD;

  $tagParms   = [
    'name' => 'The name: its first and last word give the letters. An e-mail address counts by the words before its <code>@</code> (<code>herbert.jebbink@...</code> is HJ).' ];

  $tagOptions = [
    'size'  => 'Width and height in pixels, default 48.',
    'shape' => '<code>circle</code> (default) or <code>square</code> - with rounded corners.',
    'label' => 'The accessible name (<code>aria-label</code> and <code>&lt;title&gt;</code>), by default the name.' ];

  $tagSee     = [ 'identicon', 'gravatar', 'icon' ];

?>
