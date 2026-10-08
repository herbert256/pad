<?php

  $tagAbout   = 'Frames the page with a layout instead of its directories\' wrappers.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{extends 'layout'}
{extends $layout}
PAD;

  $tagParms   = [
    'layout' => 'The layout template, named from the application root like a <code>{page}</code> (<code>.pad</code>, else <code>.html</code>). May be an expression the page\'s PHP set.' ];

  $tagOptions = [];

  $tagSee     = [ 'block', 'parent', 'meta', 'page' ];

?>
