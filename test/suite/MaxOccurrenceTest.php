<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\init_tracker;
use function mostest\add_occurrence;
use function mostest\max_occurrence;

/**
 * @depends mostest\Test\Suite\AddOccurrenceTest::testAddLetter
 */
class MaxOccurrenceTest extends TestCase {
  public function setUp() {
    $this->arr = array_map(function ($val) {
      return ['occurrences' => $val];
    }, range(-100, 100));

    $count = 0;
    do {
      shuffle($this->arr);
      $count++;
    } while ($count < 100 and end($this->arr) !== ['occurrences' => 100]);

    reset($this->arr);
  }

  public function testMaxOccurrenceArray() {
    $this->assertArraySubset([
      'occurrences' => 100,
    ], max_occurrence($this->arr));
  }

  public function testMaxOccurrenceVariadic() {
    $this->assertArraySubset([
      'occurrences' => 100,
    ], call_user_func_array('mostest\\max_occurrence', $this->arr));
  }
}
