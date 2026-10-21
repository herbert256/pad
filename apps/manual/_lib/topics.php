<?php

  // The manual's subjects by group, in the order the contents page (index, _include/
  // manualMenu.pad) and the menu of the frame (_inits.php) show them - each a page in
  // pages/, linked the way {manual 'name'} links one.

  function manualTopics () {

    return [
      'Basic concepts'         => [ 'tags', 'expressions', 'conditions', 'loops', 'trees',
                                    'examples_used_in_this_manual', 'parse', 'levels_and_occurrences',
                                    'the_content_tag', 'the_data_tag', 'php_and_html', 'properties',
                                    'field_sigils' ],
      'Constructs'             => [ 'else', 'start_end', 'content', 'page' ],
      'Text and markup'        => [ 'comments', 'whitespace', 'ignore', 'escaping_special_PAD_chars' ],
      'Advanced concepts'      => [ 'prefix', 'options_variables_parameters', 'callback', 'tag_return_values',
                                    'level_and_occurrence_variables', 'walking', 'ending_the_request', 'pipes',
                                    'sequences' ],
      'Data'                   => [ 'data_handling', 'grouping', 'pager', 'lookup', 'database', 'sqlite',
                                    'collections', 'remote_data', 'parallel_fetching', 'data_answers' ],
      'PHP helpers'            => [ 'arrays', 'strings', 'values', 'numbers', 'requests_and_sessions',
                                    'environment_and_cache', 'dates_and_logging', 'hashing_and_encryption' ],
      'Output'                 => [ 'html_helpers', 'markdown', 'showing_code', 'charts', 'locale',
                                    'fragment_cache', 'flush', 'live', 'server_sent_events' ],
      'Layouts and components' => [ 'layouts', 'page_meta', 'components', 'stacks', 'response_fragments' ],
      'The application'        => [ 'application_structure', 'extending', 'events', 'application_options',
                                    'requests_and_urls', 'clean_urls', 'sitemap', 'mail' ],
      'With JavaScript'        => [ 'htmx', 'alpine', 'vite', 'islands', 'web_components' ],
      'Forms and security'     => [ 'forms', 'client_validation', 'csrf_protection', 'flash_messages',
                                    'directory_guards', 'security_headers', 'file_uploads' ],
      'Development tools'      => [ 'debug', 'template_errors', 'toolbar', 'output_checks', 'coverage', 'replay',
                                    'live_reload', 'samples', 'typescript_types', 'app_tests', 'pad_command',
                                    'static_export', 'playground', 'editor_support', 'pad_in_the_browser',
                                    'offline_applications' ]
    ];

  }

  // The name of a subject as its link shows it: escaping_special_PAD_chars reads
  // "Escaping special PAD chars", as the {manual} tag writes it.

  function manualLabel ( $topic ) {

    return ucfirst ( str_replace ( '_', ' ', $topic ) );

  }

?>
