<?php

class Parser implements Combinator, TextParsing {

  use DerivedCombinators;

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

  static function alphaNum() {
    return self::satisfyChar(function ($c) { 
      return ctype_alnum($c);
    });
  }

  static function str($s) {
    return new Parser(function (Location $l) use ($s) {
      $index_diverted = self::firstNonMatchingIndex($l->input(), $s);
      return $index_diverted === -1 ? new Good($s, strlen($s))
                                    : new Bad($l->advanceBy($index_diverted)->toError("Expected the string '$s'"), $index_diverted !== 0 ? new Committed : new Uncommitted);
                                  
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
                                 : new Bad($l->toError("Did not satisfy predicate with input '$input'"), new Uncommitted);
    });
  }

  static function regex($pattern) {
    return new Parser(function (Location $l) use ($pattern) {
      $input = $l->input();
      $matches = array();
      return preg_match($pattern, $input, $matches) === 1 ? new Good($matches, strlen($input))
                                                          : new Bad((new Location($input, $l->offset()))->toError("Expected pattern '$pattern' to be matched by input '$input'"), new Uncommitted);
    });
  }

  function run($input) {
    $func = $this->_run;
    return $func(new Location($input));
  }

  static function label($message, Combinator $p) {
    return new Parser(function (Location $l) use ($message, $p) {
      return $p->run($l->input())->mapError(function ($e) use ($message, $l) {
        return $l->toError($message);
      });
    });
  }

  static function firstNonMatchingIndex($s1, $s2) {
    $xs = str_split($s1);
    $ys = str_split($s2);
    $i = 0;
    while ($i < count($xs) && $i < count($ys)) {
      if ($xs[$i] !== $ys[$i]) {
        return $i;
      }
      $i++;
    }
    if (count($xs) >= count($ys)) {
      return -1;
    } else {
      return count($xs);
    }
  }

  // Alternative instance

  function or_(Alternative $pb) { 
    return new Parser(function (Location $l) use ($pb) {
      $r = $this->run($l->input());
      return $r->fold(
        function (ParserError $e, CommitStatus $s) use ($pb, $l, $r) {
          return $s->fold(
            function () use ($pb, $l, $r) { return $pb->run($l->input()); },
            function () use ($r) { return $r; }
          );
        },
        function ($v) use ($r) {
          return $r;
        }
      );
    });
  }

  static function many(Combinator $p) {
    return self::lazy_product(
      $p,
      function () use ($p) { return self::many($p); }
    )->map(function ($xs) { return array_merge(array($xs[0]), $xs[1]); })
     ->or_(self::succeed(array()));
  }

  static function many1(Combinator $p) {
    return self::lazy_product(
      $p, 
      function () use ($p) { return self::many($p); }
    )->map(function ($xs) { return array_merge(array($xs[0]), $xs[1]); });
  }

  static private function _rest(Combinator $p, Combinator $sep) {
    return function ($x) use ($p, $sep) {
      return $sep->chain(function (callable $f) use ($x, $p, $sep) {
        return $p->chain(function ($y) use ($x, $f, $p, $sep) {
          $rest = self::_rest($p, $sep);
          return $rest($f($x, $y));
        });
      })->or_(self::succeed($x));
    };
  }

  static function chainl1(Combinator $p, Combinator $sep) {
    $rest = self::_rest($p, $sep);
    return $p->chain(function ($x) use ($rest) {
      return $rest($x);
    });
  }

  // Chain implementation

  function chain(callable $f) { 
    return new Parser(function (Location $l) use ($f) {
      return $this->run($l->input())->chain(function ($a, $chars_consumed) use ($f, $l) {
        $next_input = substr($l->input(), $l->offset() + $chars_consumed);
        return $f($a)->run(is_string($next_input) ? $next_input : '')
          ->mapError(function ($e) use ($chars_consumed) {
            return $e->advanceLatestErrorOffsetBy($chars_consumed);
          })
          ->setCommitStatus($chars_consumed != 0 ? new Committed : new Uncommitted)
          ->chain(function ($b, $chars_consumed_b) use ($chars_consumed) {
          return new Good($b, $chars_consumed + $chars_consumed_b);
        });
      });
    });
  }

  static function slice(Combinator $p) {
    return new Parser(function (Location $l) use ($p) {
      return $p->run($l->input())->chain(function ($_, $chars_consumed) use ($l) {
        return new Good(substr($l->input(), $l->offset(), $chars_consumed), $chars_consumed);
      });
    });
  }

}
