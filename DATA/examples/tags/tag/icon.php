<?php

  // The icons are the files of pad/icons/types/, as the engine reads them (padIconNames).

  $tagIconCount = count ( padIconNames () );

  $tagAbout   = "Writes an interface icon from PAD's own set of $tagIconCount as inline SVG, drawn in the colour of the text around it.";
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{icon 'arrow-right'/}
{icon 'trash', size=20, stroke=2, label='text'/}
{icon:star size=20/}
PAD;

  $tagParms   = [
    'name' => 'The icon, a file of <code>pad/icons/types/</code>: arrows (<code>arrow-right</code>, <code>chevron-down</code>, <code>undo</code>, <code>refresh</code> ...), marks (<code>check</code>, <code>x</code>, <code>plus</code>, <code>info</code>, <code>alert</code>, <code>star</code>, <code>heart</code> ...), layout (<code>home</code>, <code>menu</code>, <code>search</code>, <code>settings</code>, <code>table</code> ...), people and talk (<code>user</code>, <code>mail</code>, <code>chat</code>, <code>bell</code> ...), files (<code>file</code>, <code>folder</code>, <code>edit</code>, <code>trash</code>, <code>save</code> ...), media (<code>play</code>, <code>mic</code>, <code>volume</code> ...), devices and code (<code>monitor</code>, <code>cpu</code>, <code>bug</code>, <code>terminal</code> ...), money, weather and things. The icon type names it as well, <code>{icon:star/}</code>. <code>padIconNames ()</code> lists them all.' ];

  $tagOptions = [
    'size'   => 'Width and height in pixels, default 20.',
    'stroke' => 'The line width on the 24-unit grid, default 2.',
    'label'  => 'The accessible name: the icon becomes <code>role="img"</code> with <code>aria-label</code> and <code>&lt;title&gt;</code>. Without it the icon is decoration.' ];

  $tagSee     = [ 'emoji', 'rating', 'avatar' ];

?>
