<?php

trait ParsingPrimitives {

  // Functor instance

  function map(callable $f) { 
    return $this->chain(function ($a) use ($f) {
      return $this::succeed($f($a));
    });
  }

  static function times($n, Parser $p) {
    return $n > 0 ? $p->map2($p, self::times($n - 1, $p), function ($a, $as) { 
                                return array_merge(array($a), $as); 
                              })
                  : self::succeed(array());
  }

}
