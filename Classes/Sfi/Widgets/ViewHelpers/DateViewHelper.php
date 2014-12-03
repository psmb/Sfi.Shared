<?php
namespace Sfi\Widgets\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\I18n\Exception as I18nException;
use TYPO3\Flow\I18n\Formatter\DatetimeFormatter;
use TYPO3\Fluid\Core\ViewHelper\AbstractLocaleAwareViewHelper;
use TYPO3\Fluid\Core\ViewHelper\Exception as ViewHelperException;

/**
 * Formats a \DateTime object in Russian.
 */
class DateViewHelper extends \TYPO3\Fluid\ViewHelpers\Format\DateViewHelper {

	/**
	 * Render the supplied DateTime object as a formatted date.
	 *
	 * @param mixed $date either a \DateTime object or a string that is accepted by \DateTime constructor
	 * @param string $format Format String which is taken to format the Date/Time if none of the locale options are set.
	 * @param string $localeFormatType Whether to format (according to locale set in $forceLocale) date, time or datetime. Must be one of TYPO3\Flow\I18n\Cldr\Reader\DatesReader::FORMAT_TYPE_*'s constants.
	 * @param string $localeFormatLength Format length if locale set in $forceLocale. Must be one of TYPO3\Flow\I18n\Cldr\Reader\DatesReader::FORMAT_LENGTH_*'s constants.
	 * @param string $cldrFormat Format string in CLDR format (see http://cldr.unicode.org/translation/date-time)
	 * @throws ViewHelperException
	 * @return string Formatted date
	 * @api
	 */
	public function render($date = NULL, $format = 'Y-m-d', $localeFormatType = NULL, $localeFormatLength = NULL, $cldrFormat = NULL) {
		if ($date === NULL) {
			$date = $this->renderChildren();
			if ($date === NULL) {
				return '';
			}
		}
		if (!$date instanceof \DateTime) {
			try {
				$date = new \DateTime($date);
			} catch (\Exception $exception) {
				throw new ViewHelperException('"' . $date . '" could not be parsed by \DateTime constructor.', 1241722579, $exception);
			}
		}
		// Fix Russian month name, in the ugliest way possible
		$monthNames = array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
		$format = str_replace('F', $monthNames[$date->format('n')-1], $format);
		$output = parent::render($date, $format, $localeFormatType, $localeFormatLength, $cldrFormat);

		return str_replace(" 0:00", "", $output);
	}
}