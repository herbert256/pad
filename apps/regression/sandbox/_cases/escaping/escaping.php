<?php

  // What stops PAD reading braces as tags, and what happens when nothing does.
  //
  // ignore has three spellings - a tag pair, an option on a tag, a pipe on the output - and all
  // three are here, because they are implemented separately and only supposed to agree.
  //
  // The last cases are the other side of it: a brace pair nobody claims is still read as a tag,
  // and comes back as written rather than as its contents - which is what makes ignore
  // necessary in the first place - and an HTML entity passes through untouched.
  //
  // The unclaimed names are safe to use: a sandboxed pass empties the four stores on the way
  // in and restores the request's own on the way out, so nothing another case pushes - many
  // push 'a' and 'b' - can reach {a} and {b} here.


  return [

    [ 'ignore as a tag pair',
      <<<'PAD'
      {ignore}
        {not a tag}
      {/ignore}
      PAD,
      '{not a tag}' ],

    [ 'ignore keeps two of them',
      <<<'PAD'
      {ignore}
        {a} {b}
      {/ignore}
      PAD,
      '{a} {b}' ],

    [ 'ignore keeps what would otherwise run',
      <<<'PAD'
      {ignore}
        {if 1 eq 1}no{/if}
      {/ignore}
      PAD,
      '{if 1 eq 1}no{/if}' ],

    [ 'ignore as an option on a tag',
      <<<'PAD'
      {data 'xs'}
        [1]
      {/data}
      {xs ignore}
        {$xs}
      {/xs}
      PAD,
      '{$xs}' ],

    [ 'ignore as a pipe on the output',
      <<<'PAD'
      {echo 'items' | open | ignore}
      PAD,
      '{items}' ],

    [ 'a brace pair in plain text is read as a tag',
      <<<'PAD'
      a{noSuchNameAnywhere}c
      PAD,
      'a{noSuchNameAnywhere}c' ],

    [ 'an html entity is left alone',
      <<<'PAD'
      {echo '&amp;'}
      PAD,
      '&amp;' ],

    [ 'html escaping survives the pipe',
      <<<'PAD'
      {echo '<b>' | html}
      PAD,
      '&lt;b&gt;' ],

  ];

?>