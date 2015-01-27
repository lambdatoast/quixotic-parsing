<?php

class ExamplesTest extends \PHPUnit_Framework_TestCase {

  public function testParsePhpLikeArrayExample() {
    $array_keyword = Parser::str('array');
    $comma = Parser::char(',');
    $identifier = Parser::satisfyChar(function ($c) { return ctype_alnum($c); });
    $item = Parser::many1($identifier);
    $items_list = Parser::product(
      Parser::many(Parser::product($item, $comma)),
      $item
    );
    $open = Parser::char('(');
    $close = Parser::char(')');
    $array = Parser::product(
      $array_keyword,
      Parser::product(Parser::product($open, $items_list), $close)
              ->or_(Parser::product($open, $close))
    );

    $t1 = 'array(lbas2d,aaa,aaaaa)';
    $t2 = 'array(a,a,a)';
    $t3 = 'array(9)';
    $t4 = 'array()';

    $test = function ($input) use ($array) {
      $this->assertEquals(
        new Good($input, strlen($input)),
        Parser::slice($array)->run($input),
        'PHP-like array example'
      );
    };

    $test($t1);
    $test($t2);
    $test($t3);
    $test($t4);

  }
  
}
