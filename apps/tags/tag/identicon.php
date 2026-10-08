<?php

  $tagAbout   = 'Draws a GitHub-style identicon as inline SVG - a symmetric 5 by 5 pattern and a colour from a hash of the value.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{identicon $email}
{identicon $value, size=64, title='text'}
PAD;

  $tagParms   = [
    'value' => 'What the picture is made from - an e-mail address, a user id, a key. Trimmed and lower-cased before it is hashed.' ];

  $tagOptions = [
    'size'  => 'Width and height in pixels, default 64.',
    'title' => 'The accessible name, default <code>Identicon</code> - the value itself is not written into the page.' ];

  $tagSee     = [ 'avatar', 'gravatar' ];

?>
