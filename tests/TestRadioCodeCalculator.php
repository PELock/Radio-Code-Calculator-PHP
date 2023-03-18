<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface unit test
 *
 * Validate Radio Code Calculator Web API responses
 *
 * Run with PHPUnit using command:
 *
 * ./phpunit --verbose /path/radio-code-calculator/tests/TestRadioCodeCalculator.php
 *
 * Version      : v1.00
 * PHP          : >= 7
 * Dependencies : cURL, PHPUnit
 * Author       : Bartosz Wójcik (support@pelock.com);
 * Project      : https://www.pelock.com/products/radio-code-calculator
 * Homepage     : https://www.pelock.com
 *
 * @link https://www.pelock.com/products/radio-code-calculator
 * @copyright Copyright (c) 2021-2023 PELock LLC
 * @license Apache-2.0
 *
/*****************************************************************************/

namespace PELock\RadioCodeCalculator;

//
// include Radio Code Calculator API module (via composer autoloader)
//
use PELock\RadioCodeCalculator\RadioCodeCalculator;
use PELock\RadioCodeCalculator\RadioModel;
use PELock\RadioCodeCalculator\RadioModels;
use PELock\RadioCodeCalculator\RadioErrors;

//
// ...or include all the required files for PHPUnit from the relative paths
//
/*
require_once __DIR__ . "/../src/RadioCodeCalculator.php";
require_once __DIR__ . "/../src/RadioModel.php";
require_once __DIR__ . "/../src/RadioModels.php";
require_once __DIR__ . "/../src/RadioErrors.php";
*/

use PHPUnit\Framework\TestCase;

//
// make sure to provide valid activation key in order to run the tests
//
const VALID_ACTIVATION_KEY = "ABCD-ABCD-ABCD-ABCD";


final class TestRadioCodeCalculator extends TestCase
{
	/**
	 * @var RadioCodeCalculator global instance of RadioCodeCalculator
	 */
	public RadioCodeCalculator $myRadioCodeCalculator;

	/**
	 * @var string API key for the service
	 */
	public string $apiKey = "";

	protected function setUp(): void
	{
		//
		// enter valid license key here to make sure the tests will run
		//
		$this->apiKey = VALID_ACTIVATION_KEY;

		//
		// create Radio Code Calculator API class instance (we are using our activation key);
		//
		$this->myRadioCodeCalculator = new RadioCodeCalculator($this->apiKey);
	}

	function test_login(): void
	{
		// login to the service
		list($error, $result) = $this->myRadioCodeCalculator->login();

		$this->assertNotNull($result);
		$this->assertArrayHasKey('error', $result);
		$this->assertSame($result['error'], RadioErrors::SUCCESS);
		$this->assertArrayHasKey("license", $result);
		$this->assertArrayHasKey("userName", $result["license"]);
		$this->assertArrayHasKey("type", $result["license"]);
		$this->assertArrayHasKey("expirationDate", $result["license"]);
	}

	function test_login_invalid(): void
	{
		// provide invalid license key
		$radioCodeApi = new RadioCodeCalculator("AAAA-BBBB-CCCC-DDDD");

		// login to the service
		list($error, $result) = $radioCodeApi->login();

		$this->assertNotNull($result);
		$this->assertArrayHasKey('error', $result);
		$this->assertSame($result['error'], RadioErrors::INVALID_LICENSE);
	}

	function test_invalid_radio_model(): void
	{
		// login to the service
		list($error, $result) = $this->myRadioCodeCalculator->calc("INVALID RADIO MODEL", "1234");

		$this->assertNotNull($result);
		$this->assertArrayHasKey('error', $result);
		$this->assertSame($result['error'], RadioErrors::INVALID_RADIO_MODEL);
	}

	function test_radio_command(): void
	{
		// send invalid command to the service
		$params = [ "command" => "INVALID COMMAND" ];

		$result = $this->myRadioCodeCalculator->post_request($params);

		$this->assertNotNull($result);
		$this->assertArrayHasKey('error', $result);
		$this->assertSame($result['error'], RadioErrors::INVALID_COMMAND);
	}

	function test_radio_codes(): void
	{

		// valid pair of radio codes to test the calculator
		$codes = [
			[ RadioModels::get(RadioModels::RENAULT_DACIA), "Z999", "0060"],
			[ RadioModels::get(RadioModels::CHRYSLER_PANASONIC_TM9), "1234", "8865"],
			[ RadioModels::get(RadioModels::FORD_M_SERIES), "123456", "2487"],
			[ RadioModels::get(RadioModels::FORD_V_SERIES), "123456", "3067"],
			[ RadioModels::get(RadioModels::FORD_TRAVELPILOT), "1234567", "3982"],
			[ RadioModels::get(RadioModels::FIAT_STILO_BRAVO_VISTEON), "999999", "4968"],
			[ RadioModels::get(RadioModels::FIAT_DAIICHI), "6461", "8354"],
			[ RadioModels::get(RadioModels::TOYOTA_ERC), "10211376ab8e0d25", "A6905892"],
			[ RadioModels::get(RadioModels::JEEP_CHEROKEE), "TQ1AA1500E2884", "1315"],
			[ RadioModels::get(RadioModels::NISSAN_GLOVE_BOX), "D4CDDC568498", "55B7AB0BAB6F"],
			[ RadioModels::get(RadioModels::ECLIPSE_ESN), "7D4046", "15E0ED"],
		];

		foreach ($codes as $params)
		{
			$model = $params[0];
			$seed = $params[1];
			$key = $params[2];

			// offline validate input first
			$this->assertSame($model->validate($seed), RadioErrors::SUCCESS);

			// validate radio code for the given serial number
			list($error, $result) = $this->myRadioCodeCalculator->calc($model, $seed);

			$this->assertNotNull($result);
			$this->assertArrayHasKey('error', $result);
			$this->assertSame($error, RadioErrors::SUCCESS);
			$this->assertSame($result['error'], RadioErrors::SUCCESS);
			$this->assertSame($result['code'], $key);
		}
	}

	function test_radio_code_len()
	{
		// invalid radio serial length
		list($error, $result) = $this->myRadioCodeCalculator->calc(RadioModels::get(RadioModels::FORD_M_SERIES), "1");

		$this->assertNotNull($result);
		$this->assertArrayHasKey('error', $result);
		$this->assertSame($error, RadioErrors::INVALID_SERIAL_LENGTH);
		$this->assertSame($result['error'], RadioErrors::INVALID_SERIAL_LENGTH);
	}

	function test_radio_code_pattern()
	{
		// calculate the code with invalid regex pattern
		list($error, $result) = $this->myRadioCodeCalculator->calc(RadioModels::get(RadioModels::FORD_M_SERIES), "12345A");

		$this->assertNotNull($result);
		$this->assertArrayHasKey('error', $result);
		$this->assertSame($error, RadioErrors::INVALID_SERIAL_PATTERN);
		$this->assertSame($result['error'], RadioErrors::INVALID_SERIAL_PATTERN);
	}
}
