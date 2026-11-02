<?php
  // Snowball 3.2.0 generated algorithm, pinned upstream commit in scoped documentation.
  include_once PAD . 'subs/search/_lib/generated/base-stemmer.php';
  include_once PAD . 'subs/search/_lib/generated/polish-stemmer.php';
  return [
    'name' => 'Polish',
    'stopwords' => 'a ale albo ani aż bo by był była było były co czy dla do i ich im jest już jak jako je jego jej jeśli lub na nad nas nie nim o od oraz po pod się są tak także ten to tu w we z za ze że',
    'files' => [ 'subs/search/_lib/generated/base-stemmer.php', 'subs/search/_lib/generated/polish-stemmer.php' ],
    'stem' => function ( $word ) {
      static $stemmer;
      $stemmer ??= new PadSearchPolishStemmer;
      return $stemmer->stemWord ( $word );
    }
  ];
?>
