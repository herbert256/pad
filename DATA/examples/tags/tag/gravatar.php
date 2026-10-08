<?php

  $tagAbout   = 'Writes the Gravatar picture of an e-mail address as an img - only a hash of the address leaves the page - or a local avatar instead.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{gravatar $email}
{gravatar $email, size=64, default='identicon', alt='text'}
{gravatar $email, fallback='avatar', alt='text'}
PAD;

  $tagParms   = [
    'email' => 'The e-mail address - trimmed and lower-cased before it is hashed.' ];

  $tagOptions = [
    'size'     => 'Width and height in pixels, default 64; the <code>srcset</code> asks twice that for a sharp screen.',
    'default'  => 'What Gravatar shows for an address it does not know: <code>mp</code> (default), <code>identicon</code>, <code>monsterid</code>, <code>wavatar</code>, <code>retro</code>, <code>robohash</code>, <code>blank</code>, <code>404</code>, or an <code>https://</code> image address.',
    'alt'      => 'The image text - the person\'s name - default <code>Avatar</code>.',
    'fallback' => '<code>avatar</code> draws the <code>{avatar}</code> initials of <code>alt</code> (or of the address) instead, <code>identicon</code> the <code>{identicon}</code> of the address - no <code>&lt;img&gt;</code>, no outside request.' ];

  $tagSee     = [ 'avatar', 'identicon', 'img' ];

?>
