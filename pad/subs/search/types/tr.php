<?php
  // Snowball 3.2.0 generated algorithm, pinned upstream commit in scoped documentation.
  include_once PAD . 'subs/search/_lib/generated/base-stemmer.php';
  include_once PAD . 'subs/search/_lib/generated/turkish-stemmer.php';
  return [
    'name' => 'Turkish',
    'stopwords' => 'acaba ama ancak ben beni benim bir bu buna bundan bunu da daha de değil diye en gibi hem hep her hiç için ile ise kadar ki kim mi mı mu mü nasıl ne neden o olan olarak onu sen ve veya ya çok',
    'files' => [ 'subs/search/_lib/generated/base-stemmer.php', 'subs/search/_lib/generated/turkish-stemmer.php' ],
    'normalize' => fn ( $text ) => str_replace ( [ 'I', 'İ' ], [ 'ı', 'i' ], $text ),
    'stem' => function ( $word ) {
      static $stemmer;
      $stemmer ??= new PadSearchTurkishStemmer;
      return $stemmer->stemWord ( $word );
    }
  ];
?>
