<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface
 *
 * Generate radio unlocking codes for various radio players.
 *
 * Version      : v1.1.5
 * PHP          : >= 8
 * Dependencies : requests (https://pypi.python.org/pypi/requests/)
 * Author       : Bartosz Wójcik (support@pelock.com)
 * Project      : https://www.pelock.com/products/radio-code-calculator
 * Homepage     : https://www.pelock.com
 *
 * @link https://www.pelock.com/products/radio-code-calculator
 * @copyright Copyright (c) 2021-2024 PELock LLC
 * @license Apache-2.0
 *
/*****************************************************************************/

namespace PELock\RadioCodeCalculator;

/**
 * Errors returned by the Radio Code Calculator API interface
 *
 * Usage:
 *
 * if ($error === RadioErrors::SUCCESS) { ... }
 *
 */
class RadioErrors
{
	/**
	 * @var int cannot connect to the Web API interface (network error)
	 */
	const ERROR_CONNECTION = -1;

	/**
	 * @var int successful request
	 */
	const SUCCESS = 0;

	/**
	 * @var int an error occurred while validating input data (invalid length, format etc.)
	 */
	const INVALID_INPUT = 1;

	/**
	 * @var int invalid Web API command (not supported)
	 */
	const INVALID_COMMAND = 2;

	/**
	 * @var int radio model is not supported by the calculator
	 */
	const INVALID_RADIO_MODEL = 3;

	/**
	 * @var int radio serial number is invalid (invalid format, not matching the expected regex pattern)
	 */
	const INVALID_SERIAL_LENGTH = 4;

	/**
	 * @var int radio serial number doesn't match the expected regular expression pattern
	 */
	const INVALID_SERIAL_PATTERN = 5;

	/**
	 * @var int radio serial number is not supported by the selected calculator
	 */
	const INVALID_SERIAL_NOT_SUPPORTED = 6;

	/**
	 * @var int extra data is invalid (invalid format, not matching the expected regex pattern)
	 */
	const INVALID_EXTRA_LENGTH = 7;

	/**
	 * @var int extra data doesn't match the expected regular expression pattern
	 */
	const INVALID_EXTRA_PATTERN = 8;

	/**
	 * @var int license key is invalid or expired
	 */
	const INVALID_LICENSE = 100;
}
