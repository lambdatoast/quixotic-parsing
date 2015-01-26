<?php

class ParserErrorTest extends \PHPUnit_Framework_TestCase {

    public function testDataAccess() {
      $l = new Location('abcd', 42);
      $e = array($l, 99);
      $pe = new ParserError(array($e));

      $this->assertTrue(
        $pe->latest()[0]->equal($e[0])
      );

      $this->assertEquals(
        $pe->latest()[1],
        $e[1]
      );

    }

}

