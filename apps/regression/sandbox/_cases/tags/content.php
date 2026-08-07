<?php

  // Merging a content store with the content of the tag that uses it, which is five sections of
  // the manual's "The content tag" page and had no case at all.
  //
  // Two things can happen when a name in the content store is written as a tag. Either the store
  // and the tag's own content are joined - and merge= says in which order, or whether the tag's
  // content survives at all - or one of them carries an @content@ placeholder, and the other is
  // dropped into it.
  //
  // The placeholder works from either side, which is the part worth pinning: it does not matter
  // whether the store wraps the usage or the usage wraps the store, the answer is the same.

  return [

    [ 'a store is content, written by its name',
      <<<'PAD'
      {content 'c'}STORE{/content}
      {c}{/c}
      PAD,
      'STORE' ],

    // Without merge= the store comes first and the tag's own content after it.

    [ 'the store and the tag content are joined, store first',
      <<<'PAD'
      {content 'c'}STORE{/content}
      {c}BASE{/c}
      PAD,
      'STOREBASE' ],

    [ "merge='top' is that same order, said out loud",
      <<<'PAD'
      {content 'c'}STORE{/content}
      {c merge='top'}BASE{/c}
      PAD,
      'STOREBASE' ],

    [ "merge='bottom' turns them round",
      <<<'PAD'
      {content 'c'}STORE{/content}
      {c merge='bottom'}BASE{/c}
      PAD,
      'BASESTORE' ],

    [ "merge='replace' drops the tag's own content",
      <<<'PAD'
      {content 'c'}STORE{/content}
      {c merge='replace'}BASE{/c}
      PAD,
      'STORE' ],

    // @content@ is the other way of putting the two together: whichever side carries it has the
    // other dropped into it, instead of the two being joined end to end.

    [ 'the store can wrap the usage, through @content@',
      <<<'PAD'
      {content 'c'}Before-@content@-After{/content}
      {c}IN{/c}
      PAD,
      'Before-IN-After' ],

    [ 'and the usage can wrap the store, the same way',
      <<<'PAD'
      {content 'c'}IN{/content}
      {c}Before-@content@-After{/c}
      PAD,
      'Before-IN-After' ],

    // content= on any tag reads the store the same way, so merge= applies there too - which is
    // what lets a tag keep its own content and a shared block at once.

    [ 'content= takes merge= as well',
      <<<'PAD'
      {content 'c'}STORE{/content}
      {true content='c', merge='top'}BASE{/true}
      PAD,
      'STOREBASE' ],

    [ 'and the other order',
      <<<'PAD'
      {content 'c'}STORE{/content}
      {true content='c', merge='bottom'}BASE{/true}
      PAD,
      'BASESTORE' ],

    // A store may carry an @else@ of its own. Which branch of it is taken is decided by the tag
    // using it, not by the store: the same store gives its true half to a tag that hit and its
    // false half to one that did not.

    [ 'a store has its own else, and the tag decides which half',
      <<<'PAD'
      {content 'c'}yes@else@no{/content}
      {true content='c'}T@else@F{/true}
      PAD,
      'yesT' ],

    [ 'the same store against a tag that missed',
      <<<'PAD'
      {content 'c'}yes@else@no{/content}
      {false content='c'}T@else@F{/false}
      PAD,
      'noF' ],

  ];

?>