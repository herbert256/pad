<?php

  $tagAbout   = 'Declares the parameters of a custom tag, with defaults, at the top of its template.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{parms name}
{parms title, subtitle='', tone='info'}
PAD;

  $tagParms   = [
    'names' => 'The parameters, separated by commas: a name alone is required, <code>name=default</code> is optional with that default.' ];

  $tagOptions = [];

  $tagSee     = [ 'slot', 'shadow', 'attrs' ];

?>
