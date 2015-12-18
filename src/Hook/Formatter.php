<?php
namespace Transphporm\Hook;
class Formatter {
	private $formatters = [];

	public function register($formatter) {
		$this->formatters[] = $formatter;
	}

	public function format($value, $rules) {
		if (!isset($rules['format'])) return $value;
		$format = new \Transphporm\StringExtractor($rules['format']);
		$options = explode(' ', $format);
		$functionName = array_shift($options);
		foreach ($options as &$f) $f = trim($format->rebuild($f), '"');

		return $this->processFormat($options, $functionName, $value);		
	}

	private function processFormat($format, $functionName, $value) {
		foreach ($value as &$val) {
			foreach ($this->formatters as $formatter) {
				if (is_callable([$formatter, $functionName])) {
					$val = call_user_func_array([$formatter, $functionName], array_merge([$val], $format));
				}
			}
		}
		return $value;
	}
}