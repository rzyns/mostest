#!/usr/bin/env php
<?php
namespace mostest\cli;

use function mostest\get_mostest;
use function mostest\max_occurrence;

function main($argc, $argv) {
  $found_autoload = false;
  $paths = [[__DIR__, '..', 'vendor', 'autoload.php'], [__DIR__, '..', '..', '..', 'autoload.php']];

  foreach ($paths as $path) {
    $name = implode(DIRECTORY_SEPARATOR, $path);
    if (is_readable($name)) {
      require_once($name);
      $found_autoload = true;
      break;
    }
  }

  if (!$found_autoload) {
    fwrite(STDERR, 'Error: Couldn\'t find autoload.php!');
    return 404; // Heh.
  }

  if (in_array('-h', $argv) or in_array('--help', $argv)) {
    echo "Usage: {$argv[0]} [-h] [files...]",               PHP_EOL,
         "  Read files or STDIN and find the word with",    PHP_EOL,
         "  the most repeated letters. Use '-' to ",        PHP_EOL,
         "  explicitly specify STDIN in the list of files", PHP_EOL,
         PHP_EOL;

    return 0;
  }

  $files = filter_flags(array_slice($argv, 1));

  if (in_array('-', $argv)) {
    $files[] = 'php://stdin';
  }

  if (!count($files)) {
    $files = ['php://stdin'];
  }

  $handles = open_files($files);

  foreach ($handles as $filename => $handle) {
    if (!$handle) {
      fwrite(STDERR, "WARNING: Couldn't open stream '{$filename}'" . PHP_EOL);
      continue;
    }

    $mostestes[] = array_merge(get_mostest($handle), ['file' => $filename]);
  }

  $mostest = max_occurrence($mostestes);

  echo <<<TXT
Word:        {$mostest['word']}
Letter:      {$mostest['letter']}
Occurrences: {$mostest['occurrences']}
File:        {$mostest['file']}

TXT;
}

if (php_sapi_name() == 'cli')
  exit(main(count($argv), $argv));
