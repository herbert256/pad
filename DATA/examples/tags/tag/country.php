<?php

  $tagAbout   = 'Writes a country\'s flag from its ISO 3166 code or English name, optionally with the name beside it.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{country 'NL'}
{country $code, name}
PAD;

  $tagParms   = [
    'country' => 'An alpha-2 code (<code>NL</code>), an alpha-3 code (<code>NLD</code>) or the English name, in any case.' ];

  $tagOptions = [
    'name' => 'Bare option: writes the English name beside the flag; the flag is then decoration (<code>aria-hidden</code>).' ];

  $tagSee     = [ 'map', 'emoji' ];

?>
