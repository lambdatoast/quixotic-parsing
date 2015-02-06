<?php

class ParserFunctorTest extends \PHPUnit_Framework_TestCase {

  public function testMap() {
    $p = Parser::str('aaa')
      ->map(function ($s) { return strtoupper($s); })
      ->map(function ($s) { return strlen($s); });
    $this->assertEquals(
      new Good(3, 3),
      $p->run('aaa'),
      'Parser::map() preserves the structure and offset value.'
    );
  }

}
