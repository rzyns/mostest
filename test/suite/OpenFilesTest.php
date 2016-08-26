<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\cli\open_files;

class OpenFilesTest extends TestCase {
  public function testOpenFiles() {
    $unlink_these = [
      tempnam(sys_get_temp_dir(), 'mostest-cli-open_files-'),
      tempnam(sys_get_temp_dir(), 'mostest-cli-open_files-'),
      tempnam(sys_get_temp_dir(), 'mostest-cli-open_files-'),
      tempnam(sys_get_temp_dir(), 'mostest-cli-open_files-'),
    ];

    $files = open_files(array_merge($unlink_these, [
      'php://stdin',
      'this_file_should_not_exist',
      'php://memory',
      'nor_should_this_one',
    ]), false, $func);

    $should_be_resources = array_merge($unlink_these, ['php://stdin', 'php://memory']);

    foreach ($should_be_resources as $name) {
      $this->assertTrue(is_resource($files[$name]));
    }

    $this->assertFalse($files['this_file_should_not_exist']);
    $this->assertFalse($files['nor_should_this_one']);

    $func();

    foreach ($should_be_resources as $name) {
      $this->assertFalse(is_resource($name));
    }

    array_map('unlink', $unlink_these);
  }
}
