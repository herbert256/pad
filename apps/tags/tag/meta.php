<?php

  $tagAbout   = 'The page\'s metadata - a title, a layout, a cache time or any value - read while the page is assembled.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{meta title='Title', name=value}
{meta layout='layout', cache=600, access='admin', sitemap=false}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'layout'  => 'Frames the page with that layout, as <code>{extends}</code> does.',
    'cache'   => 'The page\'s own server-cache time in seconds, in an application with the page cache on; <code>0</code> or <code>false</code> keeps the page out of the cache.',
    'access'  => 'Kept for access rules to read with <code>padMeta ( \'access\' )</code>.',
    'sitemap' => '<code>false</code> leaves the page out of <code>{sitemap}</code>; read with <code>padMeta ( \'sitemap\', \'page\' )</code>.',
    'title'   => 'Like any other name: becomes the variable <code>$title</code>, which a wrapper\'s <code>&lt;title&gt;</code> shows.' ];

  $tagSee     = [ 'extends', 'block', 'sitemap', 'set' ];

?>
