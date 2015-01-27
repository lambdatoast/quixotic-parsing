<?php

class Parser implements Parsing, TextParsing {

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

  static function anyChar() {
    return self::satisfyChar(function ($c) { return $c !== ''; });
  }

  static function anyDigit() {
    return self::satisfyChar(function ($c) { 
      return is_numeric($c);
    });
  }

  static function str($s) {
    return new Parser(function (Location $l) use ($s) {
      return self::starts_with($l->input(), $s) ? new Good($s, strlen($s))
                                                : new Bad((new Location($l->input(), $l->offset()))->toError("Expected the string '$s'"));
    });
  }

  static function satisfyChar(callable $f) {
    return new Parser(function (Location $l) use ($f) {
      $input = substr($l->input(), 0, 1);
      $c = is_string($input) ? $input : '';
      return self::satisfyStr($f)->run($c);
    });
  }

  static function satisfyStr(callable $f) {
    return new Parser(function (Location $l) use ($f) {
      $input = $l->input();
      return $f($input) === true ? new Good($input, strlen($input))
                                 : new Bad($l->toError("Did not satisfy predicate with input '$input'"));
    });
  }

  static function regex($pattern) {
    return new Parser(function (Location $l) use ($pattern) {
      $input = $l->input();
      $matches = array();
      return preg_match($pattern, $input, $matches) === 1 ? new Good($matches, strlen($input))
                                                          : new Bad((new Location($input, $l->offset()))->toError("Expected pattern '$pattern' to be matched by input '$input'"));
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
        $next_input = substr($l->input(), $l->offset() + $chars_consumed);
        return $f($a)->run(is_string($next_input) ? $next_input : '')->chain(function ($b, $chars_consumed_b) use ($chars_consumed) {
          return new Good($b, $chars_consumed + $chars_consumed_b);
        });
      });
    });
  }

}
