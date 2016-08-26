<?php
namespace mostest;

/**
 * This file is the mostest.
 *
 * Seriously, it has the mostest.
 *
 * @package mostest
 * @author Janusz Dziurzyński <janusz@forserial.org>
 * @license MIT
 * @copyright 2016 Janusz Dziurzyński
 * @version 0
 */

/** @internal */
const ASCII_BYTE_MASK        = 0b10000000;

/** @internal */
const UTF8_CONTINUATION      = 0b10000000;
/** @internal */
const UTF8_CONTINUATION_MASK = 0b11000000;

/** @internal */
const UTF8_TWO_BYTES         = 0b11000000;
/** @internal */
const UTF8_TWO_BYTES_MASK    = 0b11100000;

/** @internal */
const UTF8_THREE_BYTES       = 0b11100000;
/** @internal */
const UTF8_THREE_BYTES_MASK  = 0b11110000;

/** @internal */
const UTF8_FOUR_BYTES        = 0b11110000;
/** @internal */
const UTF8_FOUR_BYTES_MASK   = 0b11111000;

/** @internal */
const UTF8_MIN_CONTINUATION_LENGTH = 1;
/** @internal */
const UTF8_MAX_CONTINUATION_LENGTH = 3;

/**
 * Get the word with the most occurrences of a single letter as a properties array
 *
 * @param resource|string $handle_or_string Either an open stream resource or a string
 * @return mixed[] ```
 *  [
 *   'map' => ['character' => 'count (int)'],
 *   'word' => ''
 *   'letter' => '',
 *   'occurences' => 0,
 * ]
 * ```
 */
function get_mostest($handle_or_string) {
  $handle = is_stringy($handle_or_string) ?
    fopen('data:text/plain,' . $handle_or_string, 'r') :
    $handle_or_string;

  $mostest = init_tracker();

  while (!feof($handle)) {
    $tracker = init_tracker();

    do {
      $c = get_next_utf8_character($handle);

      switch (true) {
        case is_null($c) or feof($handle):
        case preg_match('/^[\p{Z}\r\n]$/u', $c):
          break 2;
        case preg_match('/^\p{L}$/u', $c):
          $tracker = add_occurrence($tracker, $c);
          break;
        case preg_match("/^'|\p{Pd}|\p{Pc}$/u", $c):
          $tracker = add_occurrence($tracker, $c, 0);
          break;
        case $c === false:
          // TODO: Something better for invalid UTF-8 bytes
          fgetc($handle); // advance since get_next_utf8_character() doesn't here
          continue;
      }
    } while(true); // Dagner! This could possibly never stop...

    $mostest = max_occurrence($mostest, $tracker);
  }

  is_stringy($handle_or_string) and fclose($handle);

  return $mostest;
}

/**
 * Tests whether its argument is coercible into a string
 *
 * @param mixed $var The thing to test for stringiness
 * @return bool      `true` if `$var` is coercible into a string, `false` otherwise
 */
function is_stringy($var) {
  return is_null($var) or is_scalar($var) or is_callable([$var, '__toString']);
}

/**
 * Initialize a tracker array for tracking words and letter occurrences
 *
 * Create an empty tracker array structure for tracking character counts and words.
 *
 * @return mixed[] ```
 * [
 *     'map'         => [],
 *     'word'        => '',
 *     'letter'      => '',
 *     'occurrences' => 0,
 * ]
 * ```
 */
function init_tracker() {
  return array(
    'map'         => [],
    'word'        => '',
    'letter'      => '',
    'occurrences' => 0,
  );
}

/**
 * Add an occurrence of a letter to a word tracker
 *
 * Add an occurrence of a letter to a word tracker. The point value, or "weight"
 * of the letter is, by default, 1. The tracker is passed by value and the
 * updated tracker is returned.
 *
 * @param mixed[] $tracker Tracker array @see init_tracker
 * @param string  $letter  The unicode character/rune/letter to track
 * @param int     $weight  Optional, default 1. How many "points" to add for this occurrence
 * @return mixed[]         The updated instance of `$tracker`
 */
function add_occurrence(array $tracker, $letter, $weight = 1) {
  if (!is_stringy($letter)) {
    throw new \UnexpectedValueException('$letter must be coercible into a string!');
  }

  if (mb_strlen($letter) > 1) {
    throw new \LengthException('$letter must be a single (potentially multibyte) character!');
  }

  $tracker['word'] .= $letter;

  $letter = mb_strtolower($letter);

  array_key_exists($letter, $tracker['map']) ?
    $tracker['map'][$letter] += $weight :
    $tracker['map'][$letter] = $weight;

  if ($tracker['map'][$letter] > $tracker['occurrences']) {
    $tracker['letter'] = $letter;
    $tracker['occurrences'] = $tracker['map'][$letter];
  }

  return $tracker;
}

/**
 * Return the tracker with the highest occurrences element
 *
 * Given an array or parameter list of `tracker`s, return the one with the
 * highest `occurrences` value.
 *
 * @param mixed ...$tracker the first tracker
 * @return void
 */
function max_occurrence() {
  $args = func_get_args();
  if (count($args) === 1 and is_array(current($args))) {
    $args = current($args);
  }

  return array_reduce($args, function ($carry, $item) {
    if (is_array($item) and array_key_exists('occurrences', $item) and
      is_numeric($item['occurrences']) and $item['occurrences'] > $carry['occurrences'])
    {
      return $item;
    }

    return $carry;
  }, init_tracker());
}

/**
 * Get the next UTF-8 character from a stream resource, advancing the file pointer
 *
 * Get the next UTF-8 character (letter, rune, w/e) from a stream resource,
 * advancing the file pointer. If the next byte in the stream is not a valid
 * UTF-8 character or continuation, returns false and rewinds the file pointer.
 * If attempting to read beyond EOF, returns `NULL`.
 *
 * @param resource $handle   The stream from which to read characters/letters
 * @return null|bool|string  Returns the next UTF-8-valid character string,
 *                           `FALSE`, or `NULL` (if trying to read past EOF)
 */
function get_next_utf8_character($handle) {
  $pos  = ftell($handle);
  $char = fgetc($handle);

  if ($char === false) {
    return null;
  }

  switch (true) {
    case (ASCII_BYTE_MASK & ord($char)) === 0:
      return $char;
    case (UTF8_TWO_BYTES_MASK & ord($char)) === UTF8_TWO_BYTES:
      $length = 2;
      break;
    case (UTF8_THREE_BYTES_MASK & ord($char)) === UTF8_THREE_BYTES:
      $length = 3;
      break;
    case (UTF8_FOUR_BYTES_MASK & ord($char)) === UTF8_FOUR_BYTES:
      $length = 4;
      break;
    default:
      goto error;
  }

  $seq = fgets($handle, $length);

  if (is_valid_continuation($seq)) {
    return $char . $seq;
  }

error:
  fseek($handle, $pos);
  return false;
}

/**
 * Tests whether a string is a valid sequence of UTF-8 continuation characters
 *
 * @param string $chr The character sequence (as a string) of which to test the validity
 * @return bool       `true` if `$chr` is a valid UTF-8 continuation sequence, else `false`
 */
function is_valid_continuation($chr) {
  $len = strlen($chr); // we want the actual number of bytes here, not characters/runes/letters

  if ($len < UTF8_MIN_CONTINUATION_LENGTH or $len > UTF8_MAX_CONTINUATION_LENGTH) {
    // echo 'bad length', PHP_EOL;
    return false;
  }

  $valid = true;
  for ($i = 0; $i < $len; $i++) {
    $valid = $valid && ((UTF8_CONTINUATION_MASK & ord($chr{$i})) === UTF8_CONTINUATION);
  }

  return $valid;
}
