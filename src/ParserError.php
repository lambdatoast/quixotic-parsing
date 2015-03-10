<?php

class ParserError {
  var $errors = array();

  function __construct($errors) {
    $this->errors = $errors;
  }

  function latest() {
    $copy = array_merge($this->errors, array());
    return array_pop($copy);
  }

  function advanceLatestErrorOffsetBy($n) {
    $errors = $this->errors;
    $latest = array_pop($errors);
    $loc = $latest[0];
    $updated_latest = array($loc->advanceBy($n), $latest[1]);
    return new ParserError(array_merge($errors, array($updated_latest)));
  }

}


