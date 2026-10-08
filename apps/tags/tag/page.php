<?php

  $tagAbout   = 'Renders another page of the application - its PHP and its template - in place of the tag.';
  $tagGroup   = 'pages';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{page 'dir/name'}
{page 'products/42'}
{page 'name', include=false}
{page 'name', sandbox}
{page 'name', reset}
{page 'name', clean}
{page 'name', function='name'}
{page 'name', app='other', ajax=false}
PAD;

  $tagParms   = [
    'page' => 'The page, named from the application root as in a URL - <code>\'orders/list\'</code>. A directory is its index, a <code>.html</code> page counts, and a clean URL route binds its segments: <code>\'products/42\'</code> renders <code>products/[id].pad</code> with <code>$id</code> set.' ];

  $tagOptions = [
    'include'  => 'Whether the page renders bare. TRUE by default - the <code>_inits.pad</code>/<code>_exits.pad</code> wrappers are left out; <code>include=false</code> wraps it as a request would.',
    'sandbox'  => 'Bare option: the page runs isolated - it sees none of the variables and stores of the page around it, and leaves none behind.',
    'reset'    => 'Bare option: the level arrays and the stores are cleared for the pass and restored after it; the application\'s variables stay visible.',
    'clean'    => 'Bare option: the page sees everything around it, but whatever it creates or changes is undone afterwards.',
    'function' => 'Runs the page inside a PHP function of that name, so its PHP has a scope of its own; what it creates is copied out unless <code>sandbox</code> is given too.',
    'app'      => 'A page of another application. It cannot be built in this process, so it is fetched over HTTP - by the browser by default (as <a href="?tag/ajax">{ajax}</a> does).',
    'ajax'     => 'With <code>app=</code>: <code>ajax=false</code> fetches the other application\'s page on the server and inserts its body instead of leaving it to the browser.' ];

  $tagSee     = [ 'get', 'ajax', 'code', 'sandbox', 'restart' ];

?>
