<?php

  // The providers are the files of pad/subs/embeds/types/, as the engine reads them (padEmbedProviders).

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $tagAbout   = 'Embeds a video, a sound, a map, a pen or a document of another site behind a click-to-load facade - ' . count ( padEmbedProviders () ) . ' providers, nothing of theirs loaded before the click.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{embed 'https://www.youtube.com/watch?v=id', title='text'/}
{embed:vimeo 'id', title='text', at='1:30', ratio='4:3'/}
{openstreetmap 'latitude,longitude', zoom=15, title='text'/}
{embed 'https://codepen.io/user/pen/id', title='text', tab='html,result'/}
{audio 'sounds/bell.mp3', title='text'/}
PAD;

  $tagParms   = [
    'address' => 'An address one of the providers knows, a file of <code>pad/subs/embeds/types/</code>: '
                   . implode ( ', ', array_map ( fn ( $name ) => "<code>$name</code>", padEmbedProviders () ) )
                   . '. The embed type names one - <code>{embed:youtube \'id\'/}</code>, or bare, <code>{youtube \'id\'/}</code>, when nothing else has the name - and then a bare id of that provider is read as well.' ];

  $tagOptions = [
    'title'     => 'Required: the button\'s text, the frame\'s title, what a screen reader says.',
    'poster'    => 'A picture of <code>www/&lt;application&gt;/</code> (versioned as <code>{asset}</code>) in place of the drawn poster.',
    'thumbnail' => 'Bare option: the provider\'s own picture as the poster, where it has one at a known address (YouTube) - an image asked of the provider before the click.',
    'ratio'     => 'The proportion, <code>16:9</code> unless the provider has another (SoundCloud and Spotify 2:1, a PDF 4:5, a short and a TikTok 9:16, Apple Music and Apple Podcasts 4:3 - a song or an episode 5:2).',
    'at'        => 'YouTube, Vimeo, PeerTube, Dailymotion, Loom: the moment to start at - <code>90</code>, <code>1m30s</code> or <code>1:30</code>.',
    'lat'       => 'OpenStreetMap: the latitude of the place, beside <code>lon</code> - or the place as the first parameter, <code>\'52.37,4.89\'</code>.',
    'lon'       => 'OpenStreetMap: the longitude of the place.',
    'zoom'      => 'OpenStreetMap: 1 (the world) to 19 (a street), default 13.',
    'theme'     => 'Figma: the viewer\'s theme - <code>light</code> (Figma\'s default), <code>dark</code> or <code>system</code>, the visitor\'s.',
    'file'      => 'StackBlitz: the file open in the editor - a <code>?file=</code> in the address says it too.',
    'view'      => 'StackBlitz: what is shown - <code>editor</code>, <code>preview</code> or <code>both</code> (default).',
    'tab'       => 'CodePen: the panes shown - <code>result</code> (default), <code>html</code>, <code>css</code>, <code>js</code>, or two of them.' ];

  $tagSee     = [ 'video', 'map', 'img' ];

?>
