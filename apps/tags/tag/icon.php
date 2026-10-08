<?php

  $tagAbout   = 'Writes an interface icon from PAD\'s own set of 79 as inline SVG, drawn in the colour of the text around it.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{icon 'arrow-right'}
{icon 'trash', size=20, stroke=2, label='text'}
PAD;

  $tagParms   = [
    'name' => 'The icon: arrows and chevrons (<code>arrow-right</code>, <code>chevron-down</code>, <code>external</code>, <code>refresh</code> ...), marks (<code>check</code>, <code>x</code>, <code>plus</code>, <code>info</code>, <code>alert</code>, <code>star</code>, <code>heart</code> ...), layout (<code>home</code>, <code>menu</code>, <code>search</code>, <code>settings</code> ...), people (<code>user</code>, <code>mail</code>, <code>bell</code> ...), time and places, files (<code>file</code>, <code>edit</code>, <code>trash</code>, <code>download</code> ...), media, security and code. <code>padIconNames ()</code> lists them all.' ];

  $tagOptions = [
    'size'   => 'Width and height in pixels, default 20.',
    'stroke' => 'The line width on the 24-unit grid, default 2.',
    'label'  => 'The accessible name: the icon becomes <code>role="img"</code> with <code>aria-label</code> and <code>&lt;title&gt;</code>. Without it the icon is decoration.' ];

  $tagSee     = [ 'emoji', 'rating', 'avatar' ];

?>
