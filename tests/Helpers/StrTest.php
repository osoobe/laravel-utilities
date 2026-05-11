<?php

namespace Osoobe\Utilities\Tests\Helpers;

use Osoobe\Utilities\Helpers\Str;
use Osoobe\Utilities\Tests\TestCase;

class StrTest extends TestCase
{
    public function test_ucwords_lowercases_and_capitalizes(): void
    {
        $this->assertSame('Hello World', Str::ucwords('hello world'));
    }

    public function test_ucwords_handles_uppercase_input(): void
    {
        $this->assertSame('Hello World', Str::ucwords('HELLO WORLD'));
    }

    public function test_ucsnake_converts_camel_to_snake_case(): void
    {
        $result = Str::ucsnake('helloWorld');
        $this->assertStringContainsString('_', $result);
    }

    public function test_boolToString_true_returns_yes(): void
    {
        $this->assertSame('Yes', Str::boolToString(true));
    }

    public function test_boolToString_false_returns_no(): void
    {
        $this->assertSame('No', Str::boolToString(false));
    }

    public function test_boolToString_B_format_true(): void
    {
        $this->assertSame('True', Str::boolToString(true, 'B'));
    }

    public function test_boolToString_B_format_false(): void
    {
        $this->assertSame('False', Str::boolToString(false, 'B'));
    }

    public function test_nameParts_returns_object_with_name_fields(): void
    {
        $result = Str::nameParts('John Michael Doe');
        $this->assertIsObject($result);
        $this->assertSame('John', $result->first_name);
        $this->assertSame('Doe', $result->last_name);
        $this->assertSame('Michael', $result->middle_name);
    }

    public function test_nameParts_simple_two_part_name(): void
    {
        $result = Str::nameParts('Jane Smith');
        $this->assertSame('Jane', $result->first_name);
        $this->assertSame('Smith', $result->last_name);
        $this->assertSame('', $result->middle_name);
    }

    public function test_str_extends_laravel_str_slug_works(): void
    {
        $this->assertSame('hello-world', Str::slug('Hello World'));
    }
}
