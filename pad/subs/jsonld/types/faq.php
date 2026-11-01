<?php

  // {jsonld:faq data='questions'/} - an FAQPage: the questions of a page and their answers,
  // each a Question with its acceptedAnswer.
  //
  // The rows of data= are the questions: question (the Question's name, required - or name)
  // and answer (the text of its acceptedAnswer, an Answer, required). The answer is a text as
  // it stands; search engines take a few HTML tags in it - a link, a list, a paragraph.

  return [

    'type'  => 'FAQPage',

    'items' => [
      'path'    => 'mainEntity',
      'type'    => 'Question',
      'fields'  => [
        'question' => [ 'name',                'text', TRUE ],
        'answer'   => [ 'acceptedAnswer.text', 'text', TRUE ],
      ],
      'objects' => [ 'acceptedAnswer' => 'Answer' ],
    ],

  ];

?>
