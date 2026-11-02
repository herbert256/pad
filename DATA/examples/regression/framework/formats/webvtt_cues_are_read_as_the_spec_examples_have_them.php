<?php

  // The examples of the W3C WebVTT specification read as a browser's parser reads them - the
  // cues and their identifiers, the times with and without hours, the settings after the end
  // time, the voice of a <v> span with its classes, the markup kept in text and taken out of
  // plain with its character references decoded - and a STYLE block, the NOTE comments and
  // the header passed over. Each cue: id|start|end|startms-endms|voice|plain|settings.

  $vttExamples = [

    // 3.1, a cue with settings and the voices of two speakers
    "WEBVTT\n\n00:30.000 --> 00:31.500 align:right size:50%\n<v Roger Bingham>When we e-mailed—\n\n"
    . "00:32.500 --> 00:33.500 align:left size:50%\n<v Neil deGrasse Tyson><i>Laughs</i>\n",

    // 4.1, a STYLE block with its braces and a NOTE between, a cue with its identifier
    "WEBVTT\n\nSTYLE\n::cue {\n  color: papayawhip;\n}\n\nNOTE comment blocks can be used between style blocks.\n\n"
    . "hello\n00:00:00.000 --> 00:00:10.000\nHello <b>world</b>.\n\nNOTE style blocks cannot appear after the first cue.\n",

    // 4.2, voices with classes, a closed voice span before an italic one
    "WEBVTT\n\n00:00.000 --> 00:02.000\n<v.first.loud Esme>It's a blue apple tree!\n\n00:04.000 --> 00:06.000\n<v Esme>Hee!</v> <i>laughter</i>\n",

    // a header of an older file, character references, ruby and a timestamp tag
    "WEBVTT - Kind: captions\nLanguage: en\n\n1\n01:02:03.004 --> 01:02:05.000 line:0 position:10%,line-left\n"
    . "Tom &amp; Jerry &lt;3\n<ruby>漢<rt>kan</rt></ruby> <00:00:12.000>later\n"
  ];

  $vttOut = [];

  foreach ( $vttExamples as $vttText )
    foreach ( padFormatRead ( 'vtt', $vttText ) as $vttRow )
      $vttOut [] = "{$vttRow['id']}|{$vttRow['start']}|{$vttRow['end']}|{$vttRow['startms']}-{$vttRow['endms']}|{$vttRow['voice']}|"
                 . str_replace ( "\n", '/', $vttRow ['plain'] ) . "|{$vttRow['settings']}";

  $vttShow = implode ( ' ## ', $vttOut );
  $vttKept = json_encode ( padFormatRead ( 'vtt', $vttExamples [2] ) [1] ['text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

?>
