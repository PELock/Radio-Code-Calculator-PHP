<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface usage example
 *
 * In this example, we will demonstrate how to get information about the
 * specific radio calculator and its parameters (max. length & regex pattern).
 *
 * Version      : v1.1.0
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

//
// create Radio Code Calculator API class instance (we are using our activation key)
//
$myRadioCodeCalculator = new RadioCodeCalculator("ABCD-ABCD-ABCD-ABCD");

//
// query information about the radio model
//
list($error, $radio_model) = $myRadioCodeCalculator->info("ford-m-series");

if ($error === RadioErrors::SUCCESS)
{
	echo "Radio model name - " . $radio_model->name . "<br>";

	echo "Max. length of the radio serial number - " . $radio_model->serial_max_len . "<br>";
	echo "Regex pattern for the radio serial number - " . $radio_model->serial_regex_pattern() . "<br>";

	// is extra field specified?
	if ($radio_model->extra_max_len > 0)
	{
		echo "Max. length of the radio extra data - " . $radio_model->extra_max_len . "<br>";
		echo "Regex pattern for the radio extra data - " . $radio_model->extra_regex_pattern() . "<br>";
		echo "<br>";
	}
}
elseif ($error == RadioErrors::INVALID_LICENSE)
    echo "Invalid license key!";
else
    echo "Something unexpected happen while trying to login to the service (error code {error}).";
