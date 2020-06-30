<?php

require_once 'helpers.php';

// https://stackoverflow.com/questions/14994941/numbers-to-roman-numbers-with-php
function integerToRoman($number) {
  $map = array(
    'M' => 1000,
    'CM' => 900,
    'D' => 500,
    'CD' => 400,
    'C' => 100,
    'XC' => 90,
    'L' => 50,
    'XL' => 40,
    'X' => 10,
    'IX' => 9,
    'V' => 5,
    'IV' => 4,
    'I' => 1
  );
  $returnValue = '';
  while ($number > 0) {
    foreach ($map as $roman => $int) {
      if($number >= $int) {
        $number -= $int;
        $returnValue .= $roman;
        break;
      }
    }
  }
  return $returnValue;
}

class Editions {

  private static $editions;

  public static function initialize($filepath){
    self::$editions = json_decode(file_get_contents($filepath), true);
  }

  public static function all(){
    return self::$editions;
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

  public static function getByNumber($number){
    foreach(self::$editions as $key => $edition){
      if ($edition['number'] == $number){
        return $edition;
      }
    }
  }

  public static function romanNumerals($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return integerToRoman($edition['number']);
  }

  public static function getTitle($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return 'BARS ' . integerToRoman($edition['number']);
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

    return parseDate($edition['days']['from']);
  }

  public static function to($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return parseDate($edition['days']['to']);
  }

  public static function shouldDisplayOnlyMonths($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (isset($edition['days']['only_months'])) {
      return $edition['days']['only_months'];
    }
    return false;
  }

  public static function venues($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    return $edition['venues'];
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

    return parseDate($edition['call']['to']);
  }

  public static function isCallClosed($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    return strtotime('now') > strtotime($edition['call']['to']);
  }

  public static function getPressPassesDeadline($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    $editionFromDate = self::from($edition);
    $currentYear = $editionFromDate->format('Y');

    if (isset($edition['press_passes']) && isset($edition['press_passes']['deadline'])) {
      return parseDate($edition['press_passes']['deadline']);
    }

    // Defaults to 17/november of the given edition's year.
    // Why 17/november? Because it was the date of the first edition we started tracking the
    // press passes deadline date, which was 2019 edition.
    return parseDate($currentYear . '-11-17T03:00:00.000Z');
  }

  public static function getPressPassesPickupDates($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (!isset($edition['press_passes'])) {
      return array('from' => NULL, 'to' => NULL);
    }

    if (isset($edition['press_passes']['pickupFrom'])) {
      $from = parseDate($edition['press_passes']['pickupFrom']);
    }

    if (isset($edition['press_passes']['pickupTo'])) {
      $to = parseDate($edition['press_passes']['pickupTo']);
    }

    return array('from' => $from, 'to' => $to);
  }

  public static function getPressPassesPickupLocations($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (
      isset($edition['press_passes']) &&
      isset($edition['press_passes']['pickupLocations']) &&
      !empty($edition['press_passes']['pickupLocations'])
    ) {
      return $edition['press_passes']['pickupLocations'];
    }

    $venues = self::venues($edition);

    if (empty($venues)) {
      return NULL;
    }

    $venuePickupLocations = array();
    foreach($venues as $venueKey => $venueItem) {
      array_push($venuePickupLocations, $venueItem['name'] . ' (' . $venueItem['address'] . ')');
    }

    return $venuePickupLocations;
  }

  public static function getMapOfTitleByNumber() {
    $indexed = array();
    foreach (self::all() as $edition) {
      $indexed[$edition['number']] = Editions::getTitle($edition);
    }
    return $indexed;
  }
}

Editions::initialize(dirname(__FILE__) . '/editions.json');

?>
