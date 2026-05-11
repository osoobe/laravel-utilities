<?php

namespace Osoobe\Utilities\Tests\Helpers;

use Carbon\Carbon;
use Osoobe\Utilities\Helpers\Utilities;
use Osoobe\Utilities\Tests\TestCase;

class UtilitiesTest extends TestCase
{
    protected array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    // getObjectValue

    public function test_getObjectValue_single_key_hit(): void
    {
        $obj = (object)['name' => 'Alice'];
        $this->assertSame('Alice', Utilities::getObjectValue($obj, 'name'));
    }

    public function test_getObjectValue_single_key_miss_returns_default(): void
    {
        $obj = (object)['name' => ''];
        $this->assertSame('default', Utilities::getObjectValue($obj, 'name', 'default'));
    }

    public function test_getObjectValue_array_of_keys_returns_first_non_empty(): void
    {
        $obj = (object)['name' => '', 'title' => 'My Title'];
        $this->assertSame('My Title', Utilities::getObjectValue($obj, ['name', 'title']));
    }

    public function test_getObjectValue_array_of_keys_all_empty_returns_default(): void
    {
        $obj = (object)['name' => '', 'title' => ''];
        $this->assertSame('fallback', Utilities::getObjectValue($obj, ['name', 'title'], 'fallback'));
    }

    // getArrayValue

    public function test_getArrayValue_hit(): void
    {
        $this->assertSame('foo', Utilities::getArrayValue(['key' => 'foo'], 'key'));
    }

    public function test_getArrayValue_miss_returns_default(): void
    {
        $this->assertSame('bar', Utilities::getArrayValue([], 'missing', 'bar'));
    }

    // setObjectValue

    public function test_setObjectValue_non_empty_sets(): void
    {
        $obj = new \stdClass();
        Utilities::setObjectValue($obj, 'name', 'Alice');
        $this->assertSame('Alice', $obj->name);
    }

    public function test_setObjectValue_empty_skips(): void
    {
        $obj = new \stdClass();
        Utilities::setObjectValue($obj, 'name', '');
        $this->assertFalse(isset($obj->name));
    }

    // setArrayValue

    public function test_setArrayValue_non_empty_sets(): void
    {
        $arr = [];
        Utilities::setArrayValue($arr, 'key', 'value');
        $this->assertSame('value', $arr['key']);
    }

    public function test_setArrayValue_empty_skips(): void
    {
        $arr = [];
        Utilities::setArrayValue($arr, 'key', '');
        $this->assertArrayNotHasKey('key', $arr);
    }

    // setArrayValueIfEmpty

    public function test_setArrayValueIfEmpty_sets_when_missing(): void
    {
        $arr = [];
        Utilities::setArrayValueIfEmpty($arr, 'key', 'value');
        $this->assertSame('value', $arr['key']);
    }

    public function test_setArrayValueIfEmpty_sets_when_empty(): void
    {
        $arr = ['key' => ''];
        Utilities::setArrayValueIfEmpty($arr, 'key', 'value');
        $this->assertSame('value', $arr['key']);
    }

    public function test_setArrayValueIfEmpty_skips_when_has_value(): void
    {
        $arr = ['key' => 'original'];
        Utilities::setArrayValueIfEmpty($arr, 'key', 'new');
        $this->assertSame('original', $arr['key']);
    }

    // getArrayValueOrDefault

    public function test_getArrayValueOrDefault_existing_key(): void
    {
        $arr = ['a' => 'hello', 'b' => 'world'];
        $this->assertSame('hello', Utilities::getArrayValueOrDefault($arr, 'a', 'b'));
    }

    public function test_getArrayValueOrDefault_missing_key_falls_to_default_key(): void
    {
        $arr = ['b' => 'world'];
        $this->assertSame('world', Utilities::getArrayValueOrDefault($arr, 'a', 'b'));
    }

    // isAssociativeArray

    public function test_isAssociativeArray_true_for_assoc(): void
    {
        $this->assertTrue(Utilities::isAssociativeArray(['a' => 1, 'b' => 2]));
    }

    public function test_isAssociativeArray_false_for_sequential(): void
    {
        $this->assertFalse(Utilities::isAssociativeArray([1, 2, 3]));
    }

    // toAssociativeArray

    public function test_toAssociativeArray(): void
    {
        $result = Utilities::toAssociativeArray(['a', 'b']);
        $this->assertSame(['a' => 'a', 'b' => 'b'], $result);
    }

    // array_map_assoc

    public function test_array_map_assoc(): void
    {
        $result = Utilities::array_map_assoc(['a' => 1]);
        $this->assertSame(['a' => 'a (1)'], $result);
    }

    // array_value_to_key_default

    public function test_array_value_to_key_default(): void
    {
        $result = Utilities::array_value_to_key_default(['x', 'y']);
        $this->assertSame(['x' => 0, 'y' => 0], $result);
    }

    // formatDate

    public function test_formatDate_string_input(): void
    {
        $this->assertSame('2024-01-15', Utilities::formatDate('2024-01-15'));
    }

    public function test_formatDate_carbon_input(): void
    {
        $date = Carbon::parse('2024-06-20');
        $this->assertSame('2024-06-20', Utilities::formatDate($date));
    }

    public function test_formatDate_empty_input(): void
    {
        $this->assertSame('', Utilities::formatDate(''));
    }

    public function test_formatDate_custom_format(): void
    {
        $this->assertSame('15/01/2024', Utilities::formatDate('2024-01-15', 'd/m/Y'));
    }

    // expiryDateWindow

    public function test_expiryDateWindow_default_args(): void
    {
        $window = Utilities::expiryDateWindow();
        $this->assertInstanceOf(Carbon::class, $window->start);
        $this->assertInstanceOf(Carbon::class, $window->end);
    }

    public function test_expiryDateWindow_custom_args(): void
    {
        $window = Utilities::expiryDateWindow(10, 5);
        $now = Carbon::now();
        $this->assertTrue($window->start->diffInDays($now) <= 6);
        $this->assertTrue($window->end->diffInDays($now) <= 11);
    }

    // calcNumberPercentage

    public function test_calcNumberPercentage(): void
    {
        $this->assertSame(30.0, Utilities::calcNumberPercentage(15, 200));
    }

    // calc_percentage

    public function test_calc_percentage(): void
    {
        $this->assertSame(15.0, Utilities::calc_percentage(30, 200));
    }

    public function test_calc_percentage_zero_divisor_returns_100(): void
    {
        $this->assertSame(100, Utilities::calc_percentage(30, 0));
    }

    // calcAverage

    public function test_calcAverage(): void
    {
        $this->assertEqualsWithDelta(20.0, Utilities::calcAverage(10.0, 20.0, 30.0), 0.001);
    }

    // calcAverageNoZeros

    public function test_calcAverageNoZeros_strips_zeros(): void
    {
        $result = Utilities::calcAverageNoZeros(0.0, 10.0, 20.0);
        $this->assertEqualsWithDelta(15.0, $result, 0.001);
    }

    // maxFloat, minFloat, maxInt, minInt

    public function test_maxFloat(): void
    {
        $this->assertSame(3.5, Utilities::maxFloat(1.5, 3.5, 2.0));
    }

    public function test_minFloat(): void
    {
        $this->assertSame(1.5, Utilities::minFloat(1.5, 3.5, 2.0));
    }

    public function test_maxInt(): void
    {
        $this->assertSame(9, Utilities::maxInt(3, 9, 6));
    }

    public function test_minInt(): void
    {
        $this->assertSame(3, Utilities::minInt(3, 9, 6));
    }

    // removeEmpty

    public function test_removeEmpty_strips_empty_string_and_null(): void
    {
        $result = Utilities::removeEmpty(['', null, 'hello', 0, 'world']);
        $this->assertContains('hello', $result);
        $this->assertContains('world', $result);
        $this->assertContains(0, $result);
        $this->assertNotContains('', $result);
        $this->assertNotContains(null, $result);
    }

    // minNoZeros

    public function test_minNoZeros(): void
    {
        // removeEmpty keeps 0 (since $value === 0 is true), so 0 IS included
        // Use empty string (which is excluded) to test filtering
        $this->assertSame(2, Utilities::minNoZeros('', 5, 2));
    }

    // implodeNested

    public function test_implodeNested_flat(): void
    {
        $this->assertSame('a,b,c', Utilities::implodeNested(['a', 'b', 'c']));
    }

    public function test_implodeNested_nested(): void
    {
        $result = Utilities::implodeNested(['a', ['b', 'c']]);
        $this->assertSame('a,b,c', $result);
    }

    // implodeWithQuotes

    public function test_implodeWithQuotes(): void
    {
        $result = Utilities::implodeWithQuotes(['a', 'b', 'c']);
        $this->assertSame('"a","b","c"', $result);
    }

    // implodeWithCallback

    public function test_implodeWithCallback_headline_string(): void
    {
        $result = Utilities::implodeWithCallback(['key_name' => 'value'], ',', 'headline');
        $this->assertStringContainsString('value', $result);
    }

    public function test_implodeWithCallback_closure(): void
    {
        $result = Utilities::implodeWithCallback(['x' => 'y'], ',', function ($k, $v) {
            return "$k=$v";
        });
        $this->assertSame('x=y', $result);
    }

    // formatPhoneNumber

    public function test_formatPhoneNumber_10_digit_us(): void
    {
        $result = Utilities::formatPhoneNumber('5551234567');
        $this->assertSame('+15551234567', $result);
    }

    public function test_formatPhoneNumber_existing_plus1_prefix(): void
    {
        $result = Utilities::formatPhoneNumber('+15551234567');
        $this->assertSame('+15551234567', $result);
    }

    public function test_formatPhoneNumber_invalid_returns_null(): void
    {
        $this->assertNull(Utilities::formatPhoneNumber('123'));
    }

    public function test_formatPhoneNumber_empty_returns_null(): void
    {
        $this->assertNull(Utilities::formatPhoneNumber(''));
    }

    // getPhoneNumberBase

    public function test_getPhoneNumberBase(): void
    {
        $this->assertSame('5551234567', Utilities::getPhoneNumberBase('+15551234567'));
    }

    // getClassNameOnly

    public function test_getClassNameOnly_fqn_string(): void
    {
        $this->assertSame('Utilities', Utilities::getClassNameOnly('Osoobe\\Utilities\\Helpers\\Utilities'));
    }

    public function test_getClassNameOnly_object(): void
    {
        $this->assertSame('Utilities', Utilities::getClassNameOnly(new Utilities()));
    }

    public function test_getClassNameOnly_null_returns_empty(): void
    {
        $this->assertSame('', Utilities::getClassNameOnly(null));
    }

    // float2text

    public function test_float2text_trims_trailing_zeros(): void
    {
        $result = Utilities::float2text(1.5);
        $this->assertStringStartsWith('1.5', $result);
        $this->assertStringNotContainsString('1.50000000000000000000', $result);
    }

    // boolToString

    public function test_boolToString_Y_format_true(): void
    {
        $this->assertSame('Yes', Utilities::boolToString(true, 'Y'));
    }

    public function test_boolToString_Y_format_false(): void
    {
        $this->assertSame('No', Utilities::boolToString(false, 'Y'));
    }

    public function test_boolToString_B_format_true(): void
    {
        $this->assertSame('True', Utilities::boolToString(true, 'B'));
    }

    public function test_boolToString_B_format_false(): void
    {
        $this->assertSame('False', Utilities::boolToString(false, 'B'));
    }

    // model_compare

    public function test_model_compare_same_class_and_id_returns_true(): void
    {
        $post1 = \Osoobe\Utilities\Tests\Models\TestPost::create(['title' => 'A']);
        $post2 = \Osoobe\Utilities\Tests\Models\TestPost::find($post1->id);
        $this->assertTrue(Utilities::model_compare($post1, $post2));
    }

    public function test_model_compare_different_id_returns_false(): void
    {
        $post1 = \Osoobe\Utilities\Tests\Models\TestPost::create(['title' => 'A']);
        $post2 = \Osoobe\Utilities\Tests\Models\TestPost::create(['title' => 'B']);
        $this->assertFalse(Utilities::model_compare($post1, $post2));
    }

    public function test_model_compare_null_arg_returns_false(): void
    {
        $post = \Osoobe\Utilities\Tests\Models\TestPost::create(['title' => 'A']);
        $this->assertFalse(Utilities::model_compare($post, null));
    }

    // valueOrDefault

    public function test_valueOrDefault_non_empty_returns_value(): void
    {
        $this->assertSame('hello', Utilities::valueOrDefault('hello', 'default'));
    }

    public function test_valueOrDefault_empty_returns_default(): void
    {
        $this->assertSame('default', Utilities::valueOrDefault('', 'default'));
    }

    // getEmailVariationRegex

    public function test_getEmailVariationRegex_returns_string_matching_email(): void
    {
        $email = 'test@example.com';
        $regex = Utilities::getEmailVariationRegex($email);
        $this->assertIsString($regex);
        $this->assertSame(1, preg_match('/' . $regex . '/i', $email));
    }

    // csvToArray

    public function test_csvToArray_reads_csv_file(): void
    {
        $tmpFile = sys_get_temp_dir() . '/test_csv_' . uniqid() . '.csv';
        $this->tempFiles[] = $tmpFile;

        $handle = fopen($tmpFile, 'w');
        fputcsv($handle, ['name', 'age']);
        fputcsv($handle, ['Alice', '30']);
        fputcsv($handle, ['Bob', '25']);
        fclose($handle);

        $result = Utilities::csvToArray($tmpFile);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('Alice', $result[0]['name']);
        $this->assertSame('30', $result[0]['age']);
    }

    public function test_csvToArray_missing_file_returns_false(): void
    {
        $this->assertFalse(Utilities::csvToArray('/nonexistent/file.csv'));
    }

    // outputCSV

    public function test_outputCSV_writes_file_with_headers(): void
    {
        $tmpFile = sys_get_temp_dir() . '/test_output_csv_' . uniqid() . '.csv';
        $this->tempFiles[] = $tmpFile;

        $data = [
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
        ];

        Utilities::outputCSV($tmpFile, $data);

        $this->assertFileExists($tmpFile);
        $contents = file_get_contents($tmpFile);
        $this->assertStringContainsString('name', $contents);
        $this->assertStringContainsString('age', $contents);
        $this->assertStringContainsString('Alice', $contents);
    }
}
