<?php

namespace App\Classes;

use DateTime;
use DateTimeZone;

class Ics {

	private $data;
	private $ics = '';
	private $startDate;
	private $endDate;
	private $location;
	private $title;
	private $description;
	private $summary;
	private $trigger;
	private $availibility;
	private $busy;
	private $id;

	public function __construct($data = [])
	{
		$this->data = $data;
		$this->title = $data['title'];
		$this->startDate = $data['startDate'];
		$this->endDate = $data['endDate'];
		$this->timezone = @$data['timezone'];
		$this->summary = @$data['summary'];
		$this->location = @$data['location'];
		$this->description = @$data['description'];
		$this->trigger = @$data['trigger']; // M = minute, H = hour, D = Day. Format: [integer][D or H or M]
		$this->availibility = (@$data['availibility']) ? $data['availibility'] : false; // 


		if(!$this->timezone) {
			$this->timezone = config('app.timezone');
		}

		$this->init()
			->endDate()
			->uniqueId()
			->currentTimestamp()
			->location()
			->description()
			->summary()
			->startDate()
			->availibility()
			->alarm()
			->close();

		return $this;
	}


	public function get()
	{
		return $this->ics;
	}

	/**
	 * Start creating the ical
	 * 
	 * @return class
	 */
	public function init()
	{
		$this->ics = 'BEGIN:VCALENDAR'.PHP_EOL.
			'VERSION:2.0'.PHP_EOL.
			'PRODID:-//hacksw/handcal//NONSGML v1.0//EN'.PHP_EOL.
			'CALSCALE:GREGORIAN'.PHP_EOL.
			'BEGIN:VEVENT';

		return $this;
	}

	/**
	 * Add an id so iCal knows this is the one to update and track
	 * 
	 * @return class
	 */
	private function uniqueId()
	{
		$this->ics .= PHP_EOL.'UID:'.(($this->id) ? $this->id : uniqid());
		return $this;
	}

	/**
	 * Add the creation date
	 * 
	 * @return class
	 */
	private function currentTimestamp()
	{
		$this->ics .= PHP_EOL.'DTSTAMP:'.$this->dateToCal(time());
		return $this;
	}

	/**
	 * Add a location (optional)
	 * 
	 * @return class
	 */
	private function location()
	{
		if($this->location) {
			$this->ics .= PHP_EOL.'LOCATION:'.$this->escapeString($this->location);
		}
		return $this;
	}

	/**
	 * Add a description (optional)
	 * 
	 * @return class
	 */
	private function description()
	{
		if($this->description) {
			$this->ics .= PHP_EOL.'DESCRIPTION:'.$this->escapeString($this->description);
		}
		else {
			$this->ics .= PHP_EOL.'DESCRIPTION:'.$this->escapeString($this->title);
		}
		return $this;
	}

	/**
	 * Add a summary (optional)
	 * 
	 * @return class
	 */
	private function summary()
	{
		if($this->summary) {
			$this->ics .= PHP_EOL.'SUMMARY:'.$this->escapeString($this->summary);
		}
		else {
			$this->ics .= PHP_EOL.'SUMMARY:'.$this->escapeString($this->title);
		}
		return $this;
	}

	/**
	 * Add the start date
	 * 
	 * @return class
	 */
	private function startDate()
	{
		if($this->startDate) {
			$this->ics .= PHP_EOL.'DTSTART;TZID="'.$this->timezone.'":'.$this->dateToCal($this->startDate);
		}
		return $this;
	}

	/**
	 * The end date of the event
	 * 
	 * @return class
	 */
	private function endDate()
	{
		if($this->endDate) {
			$this->ics .= PHP_EOL.'DTEND;TZID="'.$this->timezone.'":'.$this->dateToCal($this->endDate);
		}
		return $this;
	}


	private function alarm()
	{
		if($this->trigger) {
			$this->ics .= PHP_EOL.'BEGIN:VALARM'.PHP_EOL.
			'ACTION:DISPLAY'.PHP_EOL.
			'DESCRIPTION:Kerstborrel'.PHP_EOL.
			'TRIGGER:-PT'.$this->trigger.PHP_EOL.
			'END:VALARM'.PHP_EOL;
		}
		return $this;
	}


	private function availibility()
	{
		if($this->availibility) {
			$this->ics .= PHP_EOL.'X-MICROSOFT-CDO-BUSYSTATUS:OOF'.PHP_EOL.
			'TRANSP:TRANSPARENT';
		}
		else {
			$this->ics .= PHP_EOL.'X-MICROSOFT-CDO-BUSYSTATUS:FREE'.PHP_EOL.
			'TRANSP:OPAQUE';
		}
		return $this;
	}

	/**
	 * close off the ical
	 * 
	 * @return class
	 */
	private function close()
	{
		$this->ics .= PHP_EOL.'END:VEVENT'.PHP_EOL.'END:VCALENDAR';
		return $this;
	}

	/**
	 * Convert to a date format for the ical
	 * 
	 * @param  timestamp
	 * @return timestamp
	 */
    function dateToCal($timestamp) 
    {
        return date('Ymd\THis', strtotime($timestamp));
    }

    /**
     * Escape some tokens
     * 
     * @param  string
     * @return string
     */
    function escapeString($string) 
    {
        return preg_replace('/([\,;])/','\\\$1', $string);
    }

    /**
     * Stream back the ical
     * 
     * @return void
     */
    function stream()
    {
    	return response($this->ics)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename='.str_slug($this->title).'.ics');
    }
}