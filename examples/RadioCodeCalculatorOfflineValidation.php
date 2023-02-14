<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface usage example
 *
 * In this example, we will demonstrate how to generate a code for a specific
 * type of car radio. This example shows how to use an extended offline
 * validation.
 *
 * Version      : v1.00
 * PHP          : >= 7
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
use PELock\RadioCodeCalculator\RadioModel;
use PELock\RadioCodeCalculator\RadioModels;

//
// create Radio Code Calculator API class instance (we are using our activation key)
//
$myRadioCodeCalculator = new RadioCodeCalculator("ABCD-ABCD-ABCD-ABCD");

//
// generate a single radio unlocking code
//
$serial = "123456";
$extra = "";

//
// select a radio model
//
$radioModel = RadioModels::get(RadioModels::FORD_M_SERIES);

//
// display radio model information, you can use it to set limits in your controls e.g.
//
// textFieldRadioSerial.maxLength = radioModel->serial_max_len
// textFieldRadioSerial.regEx = radioModel->serial_regex_pattern()
//
// (if allowed by your controls)
//
echo "Radio model $radioModel->name expects a serial number of $radioModel->serial_max_len length and {$radioModel->serial_regex_pattern()} regex pattern<br>";

// additional information
if ($radioModel->extra_max_len > 0)
{
	echo "Additionally an extra field is required with $radioModel->extra_max_len and {$radioModel->extra_regex_pattern()} regex pattern<br>";
}

//
// validate the serial number (offline) before sending the Web API request
//
$error = $radioModel->validate($serial, $extra);

if ($error !== RadioErrors::SUCCESS)
{
    if ($error === RadioErrors::INVALID_SERIAL_LENGTH)
        echo "Invalid serial number length (expected $radioModel->serial_max_len characters<br>";
    else if ($error == RadioErrors::INVALID_SERIAL_PATTERN)
        echo "Invalid serial number regular expression pattern (expected $radioModel->serial_regex_pattern() regex pattern)<br>";
    else if ($error == RadioErrors::INVALID_SERIAL_NOT_SUPPORTED)
        echo "This serial number is not supported";
    else if ($error == RadioErrors::INVALID_EXTRA_LENGTH)
        echo "Invalid extra data length (expected $radioModel->extra_max_len characters)<br>";
    else if ($error == RadioErrors::INVALID_EXTRA_PATTERN)
        echo "Invalid extra data regular expression pattern (expected $radioModel->extra_regex_pattern() regex pattern)<br>";
        
    exit(1);
}

//
// generate radio code (using Web API)
//
list($error, $result) = $myRadioCodeCalculator->calc($radioModel, $serial);

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
