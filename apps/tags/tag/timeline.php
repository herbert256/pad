<?php

  $tagAbout   = 'Draws events on a time axis as inline SVG - cards above and below the axis, spans as bars, or one under the other.';
  $tagGroup   = 'graphics';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{timeline data='name', date='field', label='field', text='field', color='field', format='Y', title='text', width=720, height=300}
{timeline data='name', from='field', to='field', date='field', label='field'}
{timeline date='field', label='field', vertical} ... {/timeline}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'data'     => 'The rows: a <code>{data}</code> store, a page array or a <code>_data</code> file, as for <code>{chart}</code>. As a pair the content is the rows - JSON, YAML, XML or CSV.',
    'date'     => 'The field of a moment: a year (<code>1969</code>), a month (<code>2026-10</code>) or anything <code>strtotime</code> reads - else the first field that reads as one.',
    'from'     => 'The field with the start of a span, drawn as a bar (with <code>to</code>).',
    'to'       => 'The field with the end of a span.',
    'label'    => 'The field with an event\'s title - else the first other text field.',
    'text'     => 'The field with the line under the title - else the next text field.',
    'color'    => 'A field that groups the events: a colour each and a legend.',
    'format'   => 'A PHP date format for the dates written - else by the date\'s precision (<code>1969</code>, <code>Oct 2026</code>, <code>Oct 8, 2026</code>).',
    'vertical' => 'Bare option: dates on the left and cards on the right, one under the other - for a long list.',
    'width'    => 'The width in pixels, default 720 (vertical 600).',
    'height'   => 'The height in pixels; by default it follows from the cards (not in the vertical form).',
    'title'    => 'The accessible name, default <code>Timeline</code>.' ];

  $tagSee     = [ 'chart', 'calendar', 'diagram' ];

?>
