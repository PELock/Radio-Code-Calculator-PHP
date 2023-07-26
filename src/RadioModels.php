<?php declare(strict_types=1);

/******************************************************************************
 *
 * Radio Code Calculator API - WebApi interface
 *
 * Generate radio unlocking codes for various radio players.
 *
 * Version      : v1.1.4
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
 * Supported radio models with the validation parameters (max. lengths & regex patterns)
 *
 * This helper class can be used to quickly perform offline validation of the radio
 * serial/seed codes before its send to the WebApi.
 *
 * Usage:
 *
 * $radioModel = RadioModels::get(RadioModels::FORD_M_SERIES)
 *
 */
class RadioModels
{
	const RENAULT_DACIA = [ "renault-dacia", 4, "/^([A-Z]{1}[0-9]{3})$/"];
	const CHRYSLER_PANASONIC_TM9 = [ "chrysler-panasonic-tm9", 4, "/^([0-9]{4})$/"];
	const FORD_M_SERIES = [ "ford-m-series", 6, "/^([0-9]{6})$/"];
	const FORD_V_SERIES = [ "ford-v-series", 6, "/^([0-9]{6})$/"];
	const FORD_TRAVELPILOT = [ "ford-travelpilot", 7, "/^([0-9]{7})$/"];
	const FIAT_STILO_BRAVO_VISTEON = [ "fiat-stilo-bravo-visteon", 6, "/^([a-zA-Z0-9]{6})$/"];
	const FIAT_DAIICHI = [ "fiat-daiichi", 4, "/^([0-9]{4})$/"];
	const TOYOTA_ERC = [ "toyota-erc", 16, "/^([a-zA-Z0-9]{16})$/"];
	const JEEP_CHEROKEE = [ "jeep-cherokee", 14, "/^([a-zA-Z0-9]{10}[0-9]{4})$/"];
	const NISSAN_GLOVE_BOX = [ "nissan-glove-box", 12, "/^([a-zA-Z0-9]{12})$/"];
	const ECLIPSE_ESN = [ "toyota-erc", 6, "/^([a-zA-Z0-9]{6})$/"];
	const JAGUAR_ALPINE = [ "jaguar-alpine", 5, "/^([0-9]{5})$/"];

	/**
	 * Create RadioModel instance from the provided template
	 *
	 * @param array $radio_model_params Radio model parameters
	 * @return RadioModel A RadioModel instance to be used in radio code generation
	 */
	static function get($radio_model_params): RadioModel
	{
		return new RadioModel($radio_model_params[0], $radio_model_params[1], $radio_model_params[2], $radio_model_params[3] ?? 0, $radio_model_params[4] ?? "");
	}
}
