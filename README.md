# Mostest

Mostest--find the words that most can't the least. That is, more specifically,
find the word that has the most occurrences of a single character in one or more
files. Even standard input! Pipe to your heart's content! Now with UTF-8 support!

## Installation

### Composer

In your `composer.json` file, make sure you have an entry for this repository:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/rzyns/mostest.git"
    }
  ]
}
```

```
$ composer install rzyns/mostest
```

### From Source

```
$ git clone git@github.com:rzyns/mostest.git
```

## Usage

```
$ bin/mostest -h
Usage: bin/mostest [-h|--help] [files...]
  Read files or STDIN and find the word with
  the most repeated letters. Use '-' to
  explicitly specify STDIN in the list of files
```

### Examples

```
$ bin/mostest test_romeo.txt
Word:        wherefore
Letter:      e
Occurrences: 3
File:        test_romeo.txt
```

```
$  bin/mostest test_romeo.txt test.txt
Word:        Fląąąąąąąka
Letter:      ą
Occurrences: 7
File:        test.txt
```

## Testing

The code coverage analysis is tracked in the repository and is in the [coverage/](coverage/) directory.

```
$ composer test
> phpunit
PHPUnit 5.5.2 by Sebastian Bergmann and contributors.

..........................................                        42 / 42 (100%)

Time: 760 ms, Memory: 6.00MB

OK (42 tests, 248 assertions)
```

## Generate Documentation

Note: there is no composer script for this. I forgot to add it. The Documentation
is tracked in the repository, so see it in the [doc/](doc/) directory.

```
$ phpdoc
```
