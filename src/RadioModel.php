<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface
 *
 * Generate radio unlocking codes for various radio players.
 *
 * Version      : v1.00
 * PHP          : >= 7
 * Dependencies : requests (https://pypi.python.org/pypi/requests/)
 * Author       : Bartosz Wójcik (support@pelock.com)
 * Project      : https://www.pelock.com/products/radio-code-calculator
 * Homepage     : https://www.pelock.com
 *
 * @link https://www.pelock.com/products/radio-code-calculator
 * @copyright Copyright (c) 2021-2023 PELock LLC
 * @license Apache-2.0
 *
/*****************************************************************************/

namespace PELock\RadioCodeCalculator;

/**
 * RadioModel class used to calculate the radio code for specified car radio/navigation
 *
 * Usage:
 *
 * // create Radio Code Calculator API class instance (we are using our activation key)
 * $myRadioCodeCalculator = new RadioCodeCalculator("ABCD-ABCD-ABCD-ABCD");
 *
 * // validate the serial number (offline) before sending the Web API request
 * $error = $radioModel->validate($serial, $extra);
 *
 * ...
 *
 * // generate radio code (using Web API)
 * list($error, $result) = $myRadioCodeCalculator->calc(RadioModels::get(RadioModels::FORD_M_SERIES), "123456");
 *
 */
class RadioModel
{
	/**
	 * @var string A single radio model with its parameters
	 */
	public string $name = "";

	/**
	 * @var int Required, valid length of the radio serial/seed number
	 */
	public int $serial_max_len = 0;

	/**
	 * @var array PCRE compatible regex patterns for the radio serial/seed number
	 */
	public array $_serial_regex_patterns = [];

	/**
	 * @var int Length of the optional param for radio code generation
	 */
	public int $extra_max_len = 0;

	/**
	 * @var array|null PCRE compatible regex patterns for the optional radio serial/seed number
	 */
	public array|null $_extra_regex_patterns = null;

	/**
	 * @var string Default programming language used to determine the format of regular expression formats
	 */
	public string $default_programming_language = "php";

	/**
	 * Return the regex pattern for the current programming language only
	 *
	 * @return string PCRE compatible regular expression or an empty string ""
	 */
	function serial_regex_pattern(): string
	{
		if (!array_key_exists($this->default_programming_language, $this->_serial_regex_patterns))
			return "";
		
		return $this->_serial_regex_patterns[$this->default_programming_language];
	}

	/**
	 * Extra field (if defined) regex pattern for the current programming language only or null
	 *
	 * @return string|null PCRE compatible regular expression or null if not required
	 */
	function extra_regex_pattern(): string|null
	{
		if ($this->_extra_regex_patterns == null)
			return null;

		if (!array_key_exists($this->default_programming_language, $this->_extra_regex_patterns))
			return null;

		return $this->_extra_regex_patterns[$this->default_programming_language];
	}

	/**
	 * Initialize RadioModel class with the radio model name, serial & extra fields max. length and regex pattern
	 *
	 * @param string $name Radio model name
	 * @param int $serial_max_len Max. serial length
	 * @param string|array $serial_regex_pattern Serial number single regex pattern or a dictionary
	 * @param int $extra_max_len Max. extra field length
	 * @param string|array|null extra_regex_pattern: Extra field single regex pattern or a dictionary
	 */
	function __construct(string $name,
						int $serial_max_len,
						string|array $serial_regex_pattern,
				 		int $extra_max_len = 0,
				 		string|array|null $extra_regex_pattern = null)
	{
		$this->name = $name;
		$this->serial_max_len = $serial_max_len;

		// create an empty dict to prevent Python re-using previous dict from previous object (!)
		$this->_serial_regex_patterns = [];

		// store the regex pattern under the key for the default programming language (compatibility)
		if (is_string($serial_regex_pattern))
			$this->_serial_regex_patterns[$this->default_programming_language] = $serial_regex_pattern;
		else if (is_array($serial_regex_pattern))
			$this->_serial_regex_patterns = $serial_regex_pattern;

		// initialize extra field
		$this->extra_max_len = $extra_max_len;
		$this->_extra_regex_patterns = null;

		if ($extra_max_len != 0)
			if (is_string($extra_regex_pattern))
				$this->_extra_regex_patterns[$this->default_programming_language] = $extra_regex_pattern;
			else if (is_array($extra_regex_pattern))
				$this->_extra_regex_patterns = $extra_regex_pattern;
	}

	/**
	 * Validate radio serial number and extra data (if provided), check their lenghts and regex patterns
	 *
	 * @param string $serial Radio serial number
	 * @param string|null $extra: Extra data (optional)
	 * @return int one of the RadioErrors values
	 */
	function validate(string $serial, string|null $extra = null): int
	{
		 if (strlen($serial) != $this->serial_max_len)
			return RadioErrors::INVALID_SERIAL_LENGTH;

		$matches = [];

		if (preg_match($this->serial_regex_pattern(), $serial, $matches) == false)
			return RadioErrors::INVALID_SERIAL_PATTERN;

		if ($extra !== null && strlen($extra) > 0)
		{
			if (strlen($extra) != $this->extra_max_len)
				return RadioErrors::INVALID_EXTRA_LENGTH;
			if (preg_match($this->extra_regex_pattern(), $extra, $matches) == false)
				return RadioErrors::INVALID_EXTRA_PATTERN;
		}

		return RadioErrors::SUCCESS;
	}
}
