<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface usage example
 *
 * In this example we will verify our activation key status.
 *
 * Version      : v1.1.3
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

//
// create Radio Code Calculator API class instance (we are using our activation key)
//
$myRadioCodeCalculator = new RadioCodeCalculator("ABCD-ABCD-ABCD-ABCD");

//
// login to the service
//
list($error, $result) = $myRadioCodeCalculator->login();

//
// result[] array holds the information about the license
//
// result["license"]["activationStatus"] - True if license is active, False on invalid/expired keys
// result["license"]["userName"] - user name/company name of the license owner
// result["license"]["type"] - license type (0 - Personal License, 1 - Company License)
// result["license"]["expirationDate"] - license expiration date (in YYYY-MM-DD format)
//
if ($error == RadioErrors::SUCCESS)
{
    echo "License activation status - " . ($result["license"]["activationStatus"] ? "True" : "False") . "<br>";
    echo "License owner - " . $result["license"]["userName"];
    echo "License type - " . ($result["license"]["type"] == 0 ? "Personal" : "Company") . "<br>";
    echo "Expiration date - " . $result["license"]["expirationDate"] . "<br>";
}
else if ($error == RadioErrors::INVALID_LICENSE)
    echo "Invalid license key!";
else
    echo "Something unexpected happen while trying to login to the service (error code {error}).";
