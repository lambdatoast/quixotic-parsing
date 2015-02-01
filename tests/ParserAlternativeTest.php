<?php

class ParserAlternativeTest extends \PHPUnit_Framework_TestCase {

    public function testAlternative() {
      $ab = Parser::str('ab');
      $cd = Parser::str('cd');
      $ad = Parser::str('ad');

      $this->assertEquals(
        new Good('ab', 2),
        $ab->or_($cd)->run('ab'),
        'Parser::or_(p, q) returns p when p is successful'
      );

      $this->assertEquals(
        new Good('ab', 2),
        $cd->or_($ab)->run('ab'),
        'Parser::or_(p, q) returns q when p fails uncommitted, and q is successful'
      );

      $this->assertEquals(
        new Bad((new Location('ab', 1))->toError("Expected the string 'ad'"), new Committed),
        $ad->or_($ab)->run('ab'),
        'Parser::or_(p, q) fails when p fails committed, regardless of what q is'
      );
    }

}

