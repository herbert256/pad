<?php

  $tagAbout   = 'Writes a month as a calendar table with its events, or hands over its weeks and days as rows for markup of your own.';
  $tagGroup   = 'graphics';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:calendar/}
{pad:calendar '2026-10', data='name', date='field', title='field', link='field', query='month', sunday/}
{pad:calendar '2026-10', holidays='nl'/}
{pad:calendar '2026-10', data='name'} ... {array:days} ... {array:events} ... {/array:events} ... {/array:days} ... {/pad:calendar}
PAD;

  $tagParms   = [
    'month' => 'The month, written <code>2026-10</code>. Left out: the request value named by <code>query</code>, else this month.' ];

  $tagOptions = [
    'data'   => 'The events: a <code>{pad:data}</code> store, a page array or a <code>_data</code> file, as for <code>{chart:bar}</code>.',
    'date'   => 'The field with an event\'s date - by default the first field that reads as one.',
    'title'  => 'The field with an event\'s text - by default the first other text field.',
    'link'   => 'A field with a link for the event - http(s), a page or an anchor; never <code>javascript:</code> or <code>data:</code>.',
    'query'  => 'The request value the links to the months around set, and that picks the month when no parameter is given; default <code>month</code>.',
    'sunday' => 'Bare option: the weeks start on Sunday instead of Monday.',
    'holidays' => 'A country\'s public holidays marked and named in the month - <code>\'nl\'</code>, or with its region <code>\'de-by\'</code>; see <code>{pad:holidays}</code>.' ];

  $tagSee     = [ 'chart:bar', 'timeline', 'pager', 'holidays' ];

?>
