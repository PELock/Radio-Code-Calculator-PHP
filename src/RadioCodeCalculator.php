<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface
 *
 * Generate radio unlocking codes for various radio players.
 *
 * Version      : v1.1.3
 * PHP          : >= 8
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
 * Radio Code Calculator API module
 *
 * Usage:
 *
 * myRadioCodeCalculator = new RadioCodeCalculator("YOUR-WEB-API-KEY");
 *
 */
class RadioCodeCalculator
{
	/**
	 * @var string default Radio Code Calculator API WebApi endpoint
	 */
	const API_URL = "https://www.pelock.com/api/radio-code-calculator/v1";

	/**
	 * @var string|null WebApi key for the service
	 */
	public string|null $_apiKey = null;

	/**
	 * Initialize Radio Code Calculator API class
	 *
	 * @param string|null $api_key Activation key for the service (it cannot be empty!)
	 */
	function __construct(string|null $api_key = null)
	{
		$this->_apiKey = $api_key;
	}

	/**
	 * Login to the service and get the information about the current license limits
	 *
	 * @return array A list with an error code, and an optional dictionary with the raw results (or null on error)
	 */
	function login(): array
	{
		// parameters
		$params["command"] = "login";

		$result = $this->post_request($params);

		return [ $result["error"], $result ];
	}

	/**
	 * Calculate the radio code for the selected radio model
	 *
	 * @param RadioModel|string $radio_model Radio model either as a RadioModel class or a string
	 * @param string $radio_serial_number Radio serial number / pre code
	 * @param string $radio_extra_data Optional extra data (for example - a supplier code) to generate the radio code
	 * @return array A list with an error code, and an optional dictionary with the raw results (or null)
	 */
	function calc(string|RadioModel $radio_model, string $radio_serial_number, string $radio_extra_data = ""): array
	{
		// parameters
		$params["command"] = "calc";
		$params["radio_model"] = is_string($radio_model) ? $radio_model : $radio_model->name;
		$params["serial"] = $radio_serial_number;
		$params["extra"] = $radio_extra_data;

		$result = $this->post_request($params);

		return [ $result["error"], $result ];
	}

	/**
	 * Get the information about the given radio calculator and its parameters (name, max. len & regex pattern)
	 *
	 * @param RadioModel|string $radio_model Radio model either as a RadioModel class or a string
	 * @return array A list with an error code, and an optional RadioModel create from the return values (or null)
	 */
	function info(RadioModel|string $radio_model): array
	{
		// parameters
		$params["command"] = "info";
		$params["radio_model"] = is_string($radio_model) ? $radio_model : $radio_model->name;

		// send request
		$result = $this->post_request($params);

		if ($result["error"] !== RadioErrors::SUCCESS)
			return [ $result["error"], null ];

		$model = new RadioModel($params["radio_model"], $result["serialMaxLen"], $result["serialRegexPattern"], $result["extraMaxLen"], $result["extraRegexPattern"]);

		return [ $result["error"], $model ];
	}

	/**
	 * List all the supported radio calculators and their parameters (name, max. len & regex pattern)
	 *
	 * @return array A list with an error code, and an optional list of supported RadioModels (or null)
	 */
	function list(): array
	{
		// parameters
		$params["command"] = "list";

		// send request
		$result = $this->post_request($params);

		if ($result["error"] !== RadioErrors::SUCCESS)
			return [ $result["error"], null ];

		$radio_models = [];

		// enumerate supported radio models and build a list of RadioModel classes
		foreach ($result["supportedRadioModels"] as $radio_model_name => $radio_model)
		{
			$model = new RadioModel($radio_model_name, $radio_model["serialMaxLen"],
									$radio_model["serialRegexPattern"], $radio_model["extraMaxLen"],
									$radio_model["extraRegexPattern"]);
			$radio_models[] = $model;
		}

		return [$result["error"], $radio_models];
	}

	/**
	 * Send a POST request to the server
	 *
	 * @param array $params_array An array with the parameters
	 * @return array An array with the POST request results (or default error)
	 */
	public function post_request(array $params_array): array
	{
		// add activation key to the parameters array
		if (!empty($this->_apiKey))
		{
			$params_array["key"] = $this->_apiKey;
		}

		// default error -> only returned by the SDK
		$default_error = [ "error" => RadioErrors::ERROR_CONNECTION ];


		if (!function_exists('curl_version'))
		{
			return $default_error;
		}

		$ch = curl_init();

		// URL
		curl_setopt($ch, CURLOPT_URL, self::API_URL);

		// send POST request
		curl_setopt($ch, CURLOPT_POST, true);

		// POST parameters
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params_array);

		// user agent
		curl_setopt($ch, CURLOPT_USERAGENT, "PELock Radio Code Calculator");

		// return only result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// execute query
		$response = curl_exec($ch);

		// close the session
		curl_close($ch);

		if ($response === false)
		{
			return $default_error;
		}

		// check the result
		if (empty($response)) return $default_error;

		// decode to array
		$result = json_decode($response, true);

		if (empty($result)) return $default_error;

		return $result;
	}
}
