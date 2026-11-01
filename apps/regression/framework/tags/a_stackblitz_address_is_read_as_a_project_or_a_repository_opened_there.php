<?php

  // The addresses of StackBlitz - a project, a repository of GitHub opened there with a branch
  // and a folder after it or not, a ?file= to open - are read as the route of the project and
  // the file. A repository without its name, a path climbing out of the repository, a page of
  // StackBlitz that is no project and a host that only looks like StackBlitz's are none of
  // its addresses.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( function ( $url ) {
    $found = padEmbedFind ( $url );
    return $found ? "{$found [0]}:{$found [1] ['route']}" . ( $found [1] ['file'] !== '' ? "?{$found [1] ['file']}" : '' ) : 'none';
  }, [
    'https://stackblitz.com/edit/vitejs-vite-y8mdxg',
    'https://stackblitz.com/edit/vitejs-vite-y8mdxg?file=src/main.js',
    'https://stackblitz.com/github/vitejs/vite',
    'https://stackblitz.com/github/vitejs/vite/tree/main/packages/create-vite/template-vanilla',
    'https://stackblitz.com/github/vitejs',
    'https://stackblitz.com/github/vitejs/vite/tree/../../edit/x',
    'https://stackblitz.com/pricing',
    'https://stackblitz.com.example.org/edit/vitejs-vite-y8mdxg',
  ] ) );

?>
