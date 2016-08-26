<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\init_tracker;
use function mostest\add_occurrence;

class AddOccurrenceTest extends TestCase {
  public function testAddLetter() {
    $this->assertArraySubset([
      'word' => 'a',
      'letter' => 'a',
      'occurrences' => 1,
    ], add_occurrence(init_tracker(), 'a'));
  }

  /**
   * @expectedException \LengthException
   */
  public function testAttemptAddLetters() {
    add_occurrence(init_tracker(), 'asdf');
  }

  public function testUnicodeMultibyteLetter() {
    $this->assertArraySubset([
      'word' => 'ę',
      'letter' => 'ę',
      'occurrences' => 1,
    ], add_occurrence(init_tracker(), 'ę'));
  }

  /**
   * @expectedException \LengthException
   */
  public function testAttemptAddMultibyteLetters() {
    add_occurrence(init_tracker(), 'ęłą');
  }

  /**
   * @expectedException \UnexpectedValueException
   */
  public function testAttemptAddNonStringyValue() {
    add_occurrence(init_tracker(), ['foo']);
  }
}
