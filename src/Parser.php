<?php

class Parser extends Parsing {
  private $_run;

  function __construct(callable $f) {
    $this->_run = $f;
  }

  static function succeed($a) {
    return new Parser(function (Location $_) use ($a) {
      return new Good($a, 0);
    });
  }

  static function starts_with($x, $y) {
    return $y === substr($x, 0, strlen($y));
  }

  // Char -> Parser[Char]
  static function char($c) {
    return Parser::str($c)->map(function ($s) { return substr($s, 0, 1); });
  }

  static function str($s) {
    return new Parser(function (Location $l) use ($s) {
      return self::starts_with($l->input(), $s) ? new Good($s, strlen($s))
                                                : new Bad((new Location($l->input(), $l->offset()))->toError("Expected the string '$s'"));
    });
  }

  static function product(Parser $p1, Parser $p2) {
    return $p1->chain(function ($a) use ($p2) {
      return $p2->map(function ($b) use ($a) {
        return array($a, $b);
      });
    });
  }

  // String -> Result[A]
  function run($input) {
    $func = $this->_run;
    return $func(new Location($input));
  }

  // Alternative instance

  function or_(Alternative $pb) { 
    return new Parser(function (Location $l) use ($pb) {
      return $this->run($l->input())->lazy_or_(function () use ($pb, $l) { return $pb->run($l->input()); });
    });
  }

  static function map2(Parser $a, Parser $b, callable $f) {
    return $a->chain(function ($x) use ($f, $b) {
      return $b->map(function ($y) use ($f, $x) {
        return $f($x, $y);
      });
    });
  }

  // Monad instance
  // A -> Parser[B]
  function chain(callable $f) { 
    return new Parser(function (Location $l) use ($f) {
      // Result[A]
      $r = $this->run($l->input());
      return $r->chain(function ($a, $chars_consumed) use ($f, $l) {
        return $f($a)->run(substr($l->input(), $l->offset() + $chars_consumed));
      });
    });
  }

}
