<?php

namespace ACPT\Tests;

use ACPT\Utils\PHP\Date;

class DateTest extends AbstractTestCase
{
	/**
	 * @test
	 */
	public function isDateFormatValid()
	{
		$formats = [
			'cavolo' => false,
			'not allowed' => false,
			'HH:mm:ss.SSS' => true,
			'G:i' => true,
			'd/m/Y' => true,
			'j F Y' => true,
		];

		foreach ($formats as $format => $isValid){
			if($isValid){
				$this->assertTrue(Date::isDateFormatValid($format));
			} else {
				$this->assertFalse(Date::isDateFormatValid($format));
			}
		}
	}
}