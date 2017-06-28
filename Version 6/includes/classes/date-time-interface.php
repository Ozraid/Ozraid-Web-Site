<?php
namespace Ozraid\Includes\Classes;

/**
 * Date/Time API class.
 *
 * Type:         Class
 * Dependencies: DateTime
 * Description:  Simplifies PHP's date and time class libraries.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class methods:
 *                - __get()           Magic method that returns a DateTime date and time format from $this->format property, otherwise NULL.
 *                - current()         Returns the current date and time (UTC) as a DateTime object, or date and time string
 *                                    in the format specified.
 *                - create()          Returns a DateTime object, or date and time string in the format specified.
 *                - validate()        TRUE if date and time are earlier than the current date and time, otherwise FALSE.
 *                - change_timezone() Converts either a DateTime class object or the current date and time (UTC) to a different timezone,
 *                                    and returns a DateTime class object or date and time string in the format specified.
 *                - timezone()        Returns a DateTimeZone class object.
 *                - interval()        Returns a DateInterval class object.
 *                - plus()            Adds a period of time to a DateTime class, or date and time string in the format specified.
 *                - minus()           Subtracts a period of time from a DateTime class, or date and time string in the format specified.
 *                - difference()      Returns the difference between a date and time and the current date an object containing
 *                                    years, months, days, hours, minutes, and seconds.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Date_Time_Interface {
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $date_time   DateTime class set to the current date and time (UTC).
	 * @var object $format DateTime date and time formats. {
	 *   @type string year           4-digit year. i.e. '2017'.
	 *   @type string month          2-digit month. i.e. '01' = January.
	 *   @type string month_short    3-letter name of the month. i.e. 'Jan' = January.
	 *   @type string month_long     Month. i.e. 'January'.
	 *   @type string day            2-digit day. i.e. '31' = 31st day of the month.
	 *   @type string day_short      3-letter Day of the week. i.e. 'Tue' = Tuesday.
	 *   @type string day_long       Day. i.e. 'Tuesday'.
	 *   @type string date           $format->year + '-' + $format->month + '-' + $format->day. i.e. '2017-01-31'.
	 *   @type string date_text      Date in easy-to-read format. i.e. 'Tue 31 January, 2017'.
	 *   @type string date_rfc822    APRA internet text message date format. i.e. 'Tue, 31 Jan 2017'.
	 *   @type string hour           2-digit hours in 24 hour format. i.e.'20' = 8PM.
	 *   @type string min            2-digit minutes. '30' = 30 minutes.
	 *   @type string sec            2-digit seconds. '45' = 45 seconds.
	 *   @type string meridiem       2-letter 'am' or 'pm' meridiem. 'pm' = post meridiem.
	 *   @type string offset         Time offset from UTC. i.e. '+0800' = +8 hours from UTC time.
	 *   @type string time           $format->hour + ':' + $format->min + ':' + $format->sec. i.e. '20:30:45'.
	 *   @type string time_text      Time in easy-to-read format. i.e. '8:30pm'.
	 *   @type string date_time      $format->date + ' ' + $format->time. i.e. '2017-03-31 20:30:45'.
	 *   @type string date_time_text $format->date_text + ' ' + $format->time_text. i.e. 'Tue 31 January, 2017 8:30pm'.
	 *   @type string rfc822         APRA internet text message date, time, and offset format. i.e. 'Tue, 31 Jan 2017 8:30pm +0800'.
	 * }
	 */
	private $date_time;
	private $format;
	
	/**
	 * Class constructor.
	 *
	 * Stores DateTime format data in $this->format property.
	 */
	public function __construct( $args ) {
		$this->date_time = $args->DateTime;
		
		$this->format = new \stdClass();
		$this->format->year           = 'Y';
		$this->format->month          = 'm';
		$this->format->month_short    = 'M';
		$this->format->month_long     = 'F';
		$this->format->day            = 'd';
		$this->format->day_short      = 'D';
		$this->format->day_long       = 'l';
		$this->format->date           = 'Y-m-d';
		$this->format->date_text      = 'D jS M, Y';
		$this->format->date_rfc822    = 'D, d M Y';
		$this->format->hour           = 'H';
		$this->format->min            = 'i';
		$this->format->sec            = 's';
		$this->format->meridiem       = 'a';
		$this->format->offset         = 'O';
		$this->format->time           = 'H:i:s';
		$this->format->time_text      = 'g:ia';
		$this->format->date_time      = $this->date .' ' .$this->time;
		$this->format->date_time_text = $this->date_text .' ' .$this->time_text;
		$this->format->rfc822         = $this->date_rfc822 .' ' .$this->time .' ' .$this->offset;
	}
	
	/**
	 * Magic method that returns a DateTime date and time format from $this->format property, otherwise NULL.
	 *
	 * @param string $format DateTime date and time format name.
	 * @return string DateTime date and time format.
	 */
	public function __get ( $format ) {
		if ( isset ( $this->format->{$format} ) ) {
			return $this->format->{$format};
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Returns the current date and time (UTC) as a DateTime object, or date and time string in the format specified.
	 *
	 * @param string $format Optional. Date and time format. This can be:
	 *                        - The name of a DateTime date and time format stored in $this->format property
	 *                        - A PHP date and time format.
	 * @return var DateTime class set to the current date and time (UTC), or a date and time string as determined by $format.
	 */
	public function current( $format = NULL ) {
		if ( isset ( $this->format->{$format} ) ) {
			return $this->date_time->format( $this->format->{$format} );
		}
		return $this->date_time;
	}
	
	/**
	 * Returns a DateTime object, or date and time string in the format specified.
	 *
	 * @param string $format        Date and time format. This can be:
	 *                               - The name of a DateTime date and time format stored in $this->format property
	 *                               - A PHP date and time format.
	 * @param string $date_time     Date and time string.
	 * @param string $timezone      Optional. PHP timezone. Default NULL bases the PHP timezone on either server time (UTC) or
	 *                              a time offset in $date_time.
	 * @param string $return_format Optional. Date and time format. This can be:
	 *                               - The name of a DateTime date and time format stored in $this->format property
	 *                               - A PHP date and time format
	 *                               - Default NULL, which will return the DateTime class.
	 * @return var DateTime class set to the date, time, and timezone specified, or a date and time string as determined by $return_format.
	 */
	public function create( $format, $date_time, $timezone = NULL, $return_format = NULL ) {
		if ( isset ( $this->format->{$format} ) ) {
			$format = $this->format->{$format};
		}
		if ( isset ( $timezone ) ) {
			$new_date_time = \DateTime::createFromFormat( $format, $date_time, $this->timezone( $timezone ) );
		}
		else {
			$new_date_time = \DateTime::createFromFormat( $format, $date_time );
		}
		if ( isset ( $return_format ) ) {
			if ( isset ( $this->format->{$return_format} ) ) {
				$return_format = $this->format->{$return_format};
			}
			return $new_date_time->format( $return_format );
		}
		else {
			return $new_date_time;
		}
	}
	
	/**
	 * TRUE if date and time are earlier than the current date and time, otherwise FALSE.
	 *
	 * @param object $date_time DateTime class.
	 */
	public function validate( $date_time ) {
		if ( $date_time < $this->date_time ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Converts either a DateTime class object or the current date and time (UTC) to a different timezone,
	 * and returns a DateTime class object or date and time string in the format specified.
	 *
	 * @param string timezone       PHP timezone.
	 * @param string $return_format Optional. Date and time format. This can be:
	 *                               - The name of a DateTime date and time format stored in $this->format property
	 *                               - A PHP date and time format
	 *                               - Default NULL, which will return the DateTime class.
	 * @param object $date_time     Optional. DateTime class. Default = Current date and time (UTC).
	 * @return var DateTime class set to the timezone specified, or a date and time string as determined by $return_format.
	 */
	public function change_timezone( $timezone, $return_format = NULL, $date_time = NULL ) {
		if ( ! isset ( $date_time ) ) {
			$date_time = clone $this->date_time;
		}
		$date_time->setTimezone( $this->timezone( $timezone ) );
		if ( isset ( $return_format ) ) {
			if ( isset ( $this->format->{$return_format} ) ) {
				$return_format = $this->format->{$return_format};
			}
			return $date_time->format( $return_format );
		}
		else {
			return $date_time;
		}
	}
	
	/**
	 * Returns a DateTimeZone class object.
	 *
	 * @param string $timezone Optional. PHP timezone. Default 'UTC'.
	 * @return object DateTimeZone class set to the specified timezone.
	 */
	public function timezone( $timezone = NULL ) {
		if ( ! isset ( $timezone ) ) {
			return $this->timezone->getTimezone();
		}
		return new \DateTimeZone( $timezone );
	}
	
	/**
	 * Returns a DateInterval class object.
	 *
	 * The DateInterval constructor argument requires the following:
	 *  - 'P' for period is the first character
	 *  - Dates are in the format '($integer)($date)':
	 *     * 'Y' = Years
	 *     * 'M' = Months
	 *     * 'W' = Weeks
	 *     * 'D' = Days
	 *  - Times are prefixed with the letter 'T' for time, and then all times follow in the format '($integer)($time)':
	 *     * 'H' = Hours
	 *     * 'M' = Minutes
	 *     * 'S' = Seconds.
	 *
	 *  i.e. 'P1Y' = 1 year.
	 *       'P1Y6M3D' = 1 year, 6 months, and 3 days.
	 *       'P2YT12H30M' = 2 years, 12 hours, and 30 minutes.
	 *       'PT20H10M15S' = 20 hours, 10 minutes, and 15 seconds.
	 *
	 * @param string $interval DateInterval class date and time parameters.
	 * @return object DateInterval class.
	 */
	public function interval( $interval ) {
		return new \DateInterval( $interval );
	}
	
	/**
	 * Adds a period of time to a DateTime class, or date and time string in the format specified.
	 *
	 * @param string $interval  DateInterval class date and time parameters. See interval() method.
	 * @param string $return_format Optional. Date and time format. This can be:
	 *                               - The name of a DateTime date and time format stored in $this->format property
	 *                               - A PHP date and time format
	 *                               - Default NULL, which will return the DateTime class.
	 * @param object $date_time Optional. DateTime class. Default NULL = current date and time (UTC).
	 * @return var DateTime class or a date and time string with the interval added.
	 */
	public function plus( $interval, $return_format = NULL, $date_time = NULL ) {
		if ( ! isset ( $date_time ) ) {
			$date_time = clone $this->date_time;
		}
		$date_time->add( $this->interval( $interval ) );
		if ( isset ( $return_format ) ) {
			if ( isset ( $this->format->{$return_format} ) ) {
				$return_format = $this->format->{$return_format};
			}
			return $date_time->format( $return_format );
		}
		else {
			return $date_time;
		}
	}
	
	/**
	 * Subtracts a period of time from a DateTime class, or date and time string in the format specified.
	 *
	 * @param string $interval  DateInterval class date and time parameters. See interval() method.
	 * @param string $return_format Optional. Date and time format. This can be:
	 *                               - The name of a DateTime date and time format stored in $this->format property
	 *                               - A PHP date and time format
	 *                               - Default NULL, which will return the DateTime class.
	 * @param object $date_time Optional. DateTime class. Default NULL = current date and time (UTC).
	 * @return var DateTime class or a date and time string with the interval subtracted.
	 */
	public function minus( $interval, $return_format = NULL, $date_time = NULL ) {
		if ( ! isset ( $date_time ) ) {
			$date_time = clone $this->date_time;
		}
		$date_time->sub( $this->interval( $interval ) );
		if ( isset ( $return_format ) ) {
			if ( isset ( $this->format->{$return_format} ) ) {
				$return_format = $this->format->{$return_format};
			}
			return $date_time->format( $return_format );
		}
		else {
			return $date_time;
		}
	}
	
	/**
	 * Returns the difference between a date and time and the current date an object containing years, months, days, hours, minutes, and seconds.
	 *
	 * @param object $date_time DateTime class.
	 * @return object Difference in date and time. {
	 *   @type int year  Number of years.
	 *   @type int month Number of months.
	 *   @type int day   2-digit number of days.
	 *   @type int hour  2-digit number of hours in 24 hour format.
	 *   @type int min   2-digit number of minutes.
	 *   @type int sec   2-digit number of seconds.
	 * }
	 */
	public function difference( $date_time ) {
		$interval = $this->date_time->diff( $date_time );
		return (object) array (
			'year'  => $interval->format( '%y' ),
			'month' => $interval->format( '%m' ),
			'day'   => $interval->format( '%D' ),
			'hour'  => $interval->format( '%H' ),
			'min'   => $interval->format( '%I' ),
			'sec'   => $interval->format( '%S' )
		);
	}
	
}
