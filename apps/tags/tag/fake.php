<?php

  // The kinds are the files of pad/subs/fakes/types/, as the engine reads them (padFakeKinds).

  $tagAbout   = 'Makes a fake value - a name, an email, a price, an IBAN, ' . count ( padFakeKinds () ) . ' kinds - or rows of several kinds, the same on every request, for prototypes and previews.';
  $tagGroup   = 'text';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{fake 'kind', seed=7/}
{fake:kind from=1, to=6/}
{fake 'name, email', rows=5, toData='store'/}
{fake 'field:kind, field:kind', rows=3} ... {/fake}
PAD;

  $tagParms   = [
    'kind' => 'The kind, a file of <code>pad/subs/fakes/types/</code>: '
                . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padFakeKinds () ) )
                . ' - or several, each a field under its kind\'s name or as <code>field:kind</code> says. The fake type names one as well, <code>{fake:email/}</code>.' ];

  $tagOptions = [
    'rows'      => 'So many rows of the kinds - the level\'s data, for the content of the pair or for <code>toData</code>.',
    'seed'      => 'Values of their own seed: the rows <code>padFakeRows ( kinds, rows, seed )</code> gives.',
    'from'      => 'number, decimal, price: the lowest value; date: the first moment (<code>-1 year</code>); time: the first clock time (<code>08:00</code>).',
    'to'        => 'number, decimal, price: the highest value; date: the last moment (<code>now</code>); time: the last clock time (<code>18:00</code>).',
    'format'    => 'date: the format of the moment, <code>Y-m-d</code>.',
    'decimals'  => 'decimal: the places it is rounded to, 2; latitude, longitude: the places written, 6.',
    'chance'    => 'boolean: the chance of TRUE in percent, 50.',
    'words'     => 'sentence, title, slug: the number of words.',
    'sentences' => 'paragraph: the number of sentences.',
    'name'      => 'email: the name the address is made from.',
    'south'     => 'latitude: the southern edge of the band, -90.',
    'north'     => 'latitude: the northern edge of the band, 90.',
    'west'      => 'longitude: the western edge of the box, -180 - east of <code>east</code> the box crosses the 180th meridian.',
    'east'      => 'longitude: the eastern edge of the box, 180.' ];

  $tagSee     = [ 'lorem', 'data', 'datatable' ];

?>
