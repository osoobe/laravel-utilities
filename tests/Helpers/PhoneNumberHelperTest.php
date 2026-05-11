<?php

namespace Osoobe\Utilities\Tests\Helpers;

use Osoobe\Utilities\Helpers\PhoneNumberHelper;
use Osoobe\Utilities\Tests\TestCase;

class PhoneNumberHelperTest extends TestCase
{
    public function test_valid_e164_with_country_code(): void
    {
        // The default pattern matches +[digits] format with no parens after country code
        $this->assertTrue(PhoneNumberHelper::isValid('+15551234567'));
    }

    public function test_valid_10_digit_number(): void
    {
        $this->assertTrue(PhoneNumberHelper::isValid('5551234567'));
    }

    public function test_valid_uk_number(): void
    {
        $this->assertTrue(PhoneNumberHelper::isValid('+447911123456'));
    }

    public function test_invalid_too_short(): void
    {
        $this->assertFalse(PhoneNumberHelper::isValid('123'));
    }

    public function test_invalid_empty(): void
    {
        $this->assertFalse(PhoneNumberHelper::isValid(''));
    }

    public function test_strict_pattern_via_config(): void
    {
        // Set a strict pattern that only accepts exactly 10 digits
        config(['validation.phone.pattern' => '%^\d{10}$%']);
        $this->assertTrue(PhoneNumberHelper::isValid('5551234567'));
        $this->assertFalse(PhoneNumberHelper::isValid('+1 (555) 123-4567'));
    }
}
