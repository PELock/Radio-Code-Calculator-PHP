<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface usage example
 *
 * In this example, we will demonstrate how to generate a code for a specific
 * type of car radio.
 *
 * Version      : v1.00
 * PHP          : >= 8
 * Dependencies : cURL
 * Author       : Bartosz Wójcik (support@pelock.com)
 * Project      : https://www.pelock.com/products/radio-code-calculator
 * Homepage     : https://www.pelock.com
 *
 * @link https://www.pelock.com/products/radio-code-calculator
 * @copyright Copyright (c) 2021-2023 PELock LLC
 * @license Apache-2.0
 *
/*****************************************************************************/

//
// include Radio Code Calculator API module
//
use PELock\RadioCodeCalculator\RadioCodeCalculator;
use PELock\RadioCodeCalculator\RadioErrors;
use PELock\RadioCodeCalculator\RadioModels;

//
// create Radio Code Calculator API class instance (we are using our activation key)
//
$myRadioCodeCalculator = new RadioCodeCalculator("ABCD-ABCD-ABCD-ABCD");

//
// generate radio code (using Web API)
//
list($error, $result) = $myRadioCodeCalculator->calc(RadioModels::get(RadioModels::FORD_M_SERIES), "123456");

switch($error)
{
	case RadioErrors::SUCCESS: echo "Radio code is " . $result["code"]; break;
	case RadioErrors::INVALID_RADIO_MODEL: echo "Invalid radio model (not supported)"; break;
	case RadioErrors::INVALID_SERIAL_LENGTH: echo "Invalid serial number length (expected " . $result["serialMaxLen"] . " characters)"; break;
	case RadioErrors::INVALID_SERIAL_PATTERN: echo "Invalid serial number regular expression pattern (expected " . $result["serialRegexPattern"]["php"] . " regex pattern)"; break;
	case RadioErrors::INVALID_SERIAL_NOT_SUPPORTED: echo "This serial number is not supported"; break;
	case RadioErrors::INVALID_EXTRA_LENGTH: echo "Invalid extra data length (expected " . $result["extraMaxLen"] . " characters)"; break;
	case RadioErrors::INVALID_EXTRA_PATTERN: echo "Invalid extra data regular expression pattern (expected " . $result["extraRegexPattern"]["php"] . " regex pattern"; break;
	case RadioErrors::INVALID_INPUT: echo "Invalid input data"; break;
	case RadioErrors::INVALID_COMMAND: echo "Invalid command sent to the Web API interface"; break;
	case RadioErrors::INVALID_LICENSE: echo "Invalid license key!"; break;
	default: echo "Something unexpected happen while trying to login to the service (error code {error})."; break;
}