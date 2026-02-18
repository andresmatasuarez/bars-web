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

  public static function year($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    return $edition['year'];
  }

  public static function datesLabel($edition = NULL) {
    if (is_null($edition)) {
      $edition = self::current();
    }
    $from = self::from($edition);
    $to = self::to($edition);
    if ($from && $to) {
      return $from->format('j') . ' - ' . $to->format('j') . ' ' .
        ucfirst(getSpanishMonthName($to->format('F'))) . ' ' . $to->format('Y');
    }
    return self::year($edition) . ' (Fechas a confirmar)';
  }

  public static function areDatesDefined($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }
    return !is_null(self::from($edition)) && !is_null(self::to($edition));
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

    return isset($edition['days']['from']) ? parseDate($edition['days']['from']) : null;
  }

  public static function to($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return isset($edition['days']['to']) ? parseDate($edition['days']['to']) : null;
  }

  public static function shouldDisplayOnlyMonths($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (isset($edition['days']['onlyMonths'])) {
      return $edition['days']['onlyMonths'];
    }
    return false;
  }

  public static function venues($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    return isset($edition['venues']) ? $edition['venues'] : null;
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

  public static function callDeadlineExtended($edition = NULL){
    if (is_null($edition)){
      $edition = self::current();
    }

    return parseDate($edition['call']['extendedTo']);
  }

  public static function isCallClosed($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    $to = $edition['call']['to'];

    return strtotime($to) < strtotime('now');
  }

  public static function getPressPassesDeadline($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    $editionFromDate = self::from($edition);
    $currentYear = is_null($editionFromDate) ? new DateTime() : $editionFromDate;
    $currentYear = $currentYear->format('Y');

    if (isset($edition['pressPasses']) && isset($edition['pressPasses']['deadline'])) {
      return parseDate($edition['pressPasses']['deadline']);
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

    if (!isset($edition['pressPasses'])) {
      return array('from' => NULL, 'to' => NULL);
    }

    $from = isset($edition['pressPasses']['pickupFrom'])
      ? parseDate($edition['pressPasses']['pickupFrom'])
      : NULL;

    $to = isset($edition['pressPasses']['pickupTo'])
      ? parseDate($edition['pressPasses']['pickupTo'])
      : NULL;

    return array('from' => $from, 'to' => $to);
  }

  public static function getPressPassesPickupLocations($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (
      isset($edition['pressPasses']) &&
      isset($edition['pressPasses']['pickupLocations']) &&
      !empty($edition['pressPasses']['pickupLocations'])
    ) {
      return $edition['pressPasses']['pickupLocations'];
    }

    $venues = self::venues($edition);

    // keep live (non-online) venues only
    function isNonOnlineVenue($venue) {
      return !array_key_exists('online', $venue) || !$venue['online'];
    }

    $venues = array_filter($venues, 'isNonOnlineVenue');

    if (empty($venues)) {
      return NULL;
    }

    $venuePickupLocations = array();
    foreach($venues as $venueKey => $venueItem) {
      array_push($venuePickupLocations, $venueItem['name'] . ' (' . $venueItem['address'] . ')');
    }

    return $venuePickupLocations;
  }

  public static function getPressPassesAdditionalInfo($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (
      isset($edition['pressPasses']) &&
      isset($edition['pressPasses']['pickupAdditionalInfo']) &&
      !empty($edition['pressPasses']['pickupAdditionalInfo'])
    ) {
      return $edition['pressPasses']['pickupAdditionalInfo'];
    }

    return NULL;
  }

  public static function getPressPassesCredentialsFormURL($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (
      isset($edition['pressPasses']) &&
      isset($edition['pressPasses']['credentialsFormURL']) &&
      !empty($edition['pressPasses']['credentialsFormURL'])
    ) {
      return $edition['pressPasses']['credentialsFormURL'];
    }

    return NULL;
  }

  public static function lastCompleted($now = null) {
    if ($now === null) {
      $now = new DateTime();
    }
    $current = self::current();
    $from = self::from($current);

    if (!is_null($from)) {
      $threshold = clone $from;
      $threshold->modify('-7 days');
      if ($now < $threshold) {
        // Current edition hasn't started yet (not within 7 days), use previous
        return self::getByNumber($current['number'] - 1);
      }
    }

    return $current;
  }

  public static function getMapOfTitleByNumber() {
    $indexed = array();
    foreach (self::all() as $edition) {
      $indexed[$edition['number']] = Editions::getTitle($edition);
    }
    return $indexed;
  }

  public static function getAwards($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    if (isset($edition['awards']) && !empty($edition['awards'])) {
      return $edition['awards'];
    }

    return NULL;
  }

  public static function getJuries($edition = NULL) {
    if (is_null($edition)){
      $edition = self::current();
    }

    // getJuries defined in jury-post-type.php
    $juriesFromDB = getJuries($edition);

    if (!empty($juriesFromDB)) {
      return $juriesFromDB;
    }

    if (isset($edition['juries']) && !empty($edition['juries'])) {
      return $edition['juries'];
    }

    return NULL;
  }
}

Editions::initialize(dirname(__FILE__) . '/editions.json');

?>
