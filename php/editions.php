<?php

class Editions {

  private static $editions;

  public static function initialize($filepath){
    self::$editions = json_decode(file_get_contents($filepath), true);
  }

  public static function current(){
    $max = NULL;
    foreach(self::$editions as $key => $edition){
      if (is_null($max) || $edition['number'] > $max['number']){
        $max = $edition;
      }
    }

    return $max;
  }

  public static function days($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    $from = self::from($edition);
    $to   = self::to($edition);

    $one_day = new DateInterval('P1D');

    $diff = $from->diff($to);

    $days = array($from);
    while(count($days) < $diff->days + 1){
      $previous = clone $days[count($days) - 1];
      $days[] = $previous->add($one_day);
    }

    return $days;
  }

  public static function from($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return self::parseDate($edition['days']['from']);
  }

  public static function to($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return self::parseDate($edition['days']['to']);
  }

  public static function call($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return $edition['call'];
  }

  public static function callDeadline($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return self::parseDate($edition['call']['to']);
  }

  private static function parseDate($date){
    $date = new DateTime($date);
    $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
    return $date;
  }

}

Editions::initialize(dirname(__FILE__) . '/editions.json');

?>