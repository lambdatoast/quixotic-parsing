<?php

class Parser implements Parsing {

  use ParsingPrimitives;

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

  static function char($c) {
    return Parser::str($c)->map(function ($s) { return substr($s, 0, 1); });
  }

  static function str($s) {
    return new Parser(function (Location $l) use ($s) {
      return self::starts_with($l->input(), $s) ? new Good($s, strlen($s))
                                                : new Bad((new Location($l->input(), $l->offset()))->toError("Expected the string '$s'"));
    });
  }

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

  // Chain implementation

  function chain(callable $f) { 
    return new Parser(function (Location $l) use ($f) {
      return $this->run($l->input())->chain(function ($a, $chars_consumed) use ($f, $l) {
        $r = $f($a)->run(substr($l->input(), $l->offset() + $chars_consumed));
        return $r->chain(function ($b, $chars_consumed_b) use ($chars_consumed) {
          return new Good($b, $chars_consumed + $chars_consumed_b);
        });
      });
    });
  }

}
