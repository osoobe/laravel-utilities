<?php

namespace Osoobe\Utilities\Tests\Helpers;

use Illuminate\Support\Facades\Storage;
use Osoobe\Utilities\Helpers\ImageHelper;
use Osoobe\Utilities\Tests\TestCase;

class ImageHelperTest extends TestCase
{
    const TINY_PNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    public function test_base64Only_strips_prefix(): void
    {
        $result = ImageHelper::base64Only('data:image/png;base64,ABC123');
        $this->assertSame('ABC123', $result);
    }

    public function test_base64Only_no_prefix_returns_as_is(): void
    {
        $result = ImageHelper::base64Only('ABC123');
        $this->assertSame('ABC123', $result);
    }

    public function test_storeBase64Image_stores_file_and_returns_path(): void
    {
        Storage::fake('public');
        $path = ImageHelper::storeBase64Image(self::TINY_PNG, 'images');
        $this->assertNotFalse($path);
        $this->assertIsString($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_storeBase64Image_invalid_base64_returns_false(): void
    {
        Storage::fake('public');
        $result = ImageHelper::storeBase64Image('data:image/png;base64,!!!not-valid-base64!!!', 'images');
        $this->assertFalse($result);
    }

    public function test_storeBase64Image_with_explicit_filename(): void
    {
        Storage::fake('public');
        $path = ImageHelper::storeBase64Image(self::TINY_PNG, 'images', 'myfile');
        $this->assertNotFalse($path);
        $this->assertStringContainsString('myfile', $path);
        $this->assertStringContainsString('.png', $path);
    }

    public function test_storeBase64Image_without_filename_uses_img_prefix(): void
    {
        Storage::fake('public');
        $path = ImageHelper::storeBase64Image(self::TINY_PNG, 'images');
        $this->assertNotFalse($path);
        $filename = basename($path);
        $this->assertStringStartsWith('img_', $filename);
    }
}
