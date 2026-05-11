<?php

namespace Osoobe\Utilities\Tests\Helpers;

use Osoobe\Utilities\Helpers\FormatHelper;
use Osoobe\Utilities\Tests\TestCase;

class FormatHelperTest extends TestCase
{
    // formatString

    public function test_formatString_url_html_returns_anchor(): void
    {
        $result = FormatHelper::formatString('https://example.com', 'html');
        $this->assertStringContainsString('<a', $result);
        $this->assertStringContainsString('https://example.com', $result);
    }

    public function test_formatString_url_markdown_returns_link(): void
    {
        $result = FormatHelper::formatString('https://example.com', 'markdown');
        $this->assertStringContainsString('[', $result);
        $this->assertStringContainsString('](https://example.com)', $result);
    }

    public function test_formatString_email_returns_mailto_html(): void
    {
        $result = FormatHelper::formatString('user@example.com', 'html');
        $this->assertStringContainsString('mailto:', $result);
        $this->assertStringContainsString('<a', $result);
    }

    public function test_formatString_phone_returns_tel_html(): void
    {
        $result = FormatHelper::formatString('+15551234567', 'html');
        $this->assertStringContainsString('tel:', $result);
    }

    public function test_formatString_plain_text_returns_plain(): void
    {
        $result = FormatHelper::formatString('just some text');
        $this->assertSame('just some text', $result);
    }

    // formatLink

    public function test_formatLink_html_with_title(): void
    {
        $result = FormatHelper::formatLink('https://example.com', 'html', 'Click here', false);
        $this->assertStringContainsString('<a', $result);
        $this->assertStringContainsString('Click here', $result);
        $this->assertStringContainsString('https://example.com', $result);
    }

    public function test_formatLink_markdown_with_title(): void
    {
        $result = FormatHelper::formatLink('https://example.com', 'markdown', 'Click here', false);
        $this->assertSame('[Click here](https://example.com)', $result);
    }

    public function test_formatLink_string_default(): void
    {
        $result = FormatHelper::formatLink('https://example.com', 'string', null, false);
        $this->assertSame('https://example.com', $result);
    }

    public function test_formatLink_check_true_non_url_falls_back(): void
    {
        $result = FormatHelper::formatLink('not a url', 'html', null, true);
        $this->assertStringNotContainsString('<a', $result);
        $this->assertSame('not a url', $result);
    }

    // formatEmail

    public function test_formatEmail_html_format(): void
    {
        $result = FormatHelper::formatEmail('user@example.com', 'html', null, false);
        $this->assertStringContainsString('mailto:', $result);
        $this->assertStringContainsString('<a', $result);
    }

    public function test_formatEmail_markdown_format(): void
    {
        $result = FormatHelper::formatEmail('user@example.com', 'markdown', null, false);
        $this->assertStringContainsString('mailto:', $result);
        $this->assertStringContainsString('[', $result);
    }

    public function test_formatEmail_invalid_email_check_true_falls_back(): void
    {
        $result = FormatHelper::formatEmail('not-an-email', 'html', null, true);
        $this->assertStringNotContainsString('mailto:', $result);
        $this->assertSame('not-an-email', $result);
    }

    public function test_formatEmail_unsupported_format_returns_plain(): void
    {
        $result = FormatHelper::formatEmail('user@example.com', 'xml', null, false);
        $this->assertSame('user@example.com', $result);
    }

    // formatPhone

    public function test_formatPhone_html_format(): void
    {
        $result = FormatHelper::formatPhone('+15551234567', 'html', null, false);
        $this->assertStringContainsString('tel:', $result);
        $this->assertStringContainsString('<a', $result);
    }

    public function test_formatPhone_markdown_format(): void
    {
        $result = FormatHelper::formatPhone('+15551234567', 'markdown', null, false);
        $this->assertStringContainsString('tel:', $result);
        $this->assertStringContainsString('[', $result);
    }

    public function test_formatPhone_unsupported_format_returns_plain(): void
    {
        $result = FormatHelper::formatPhone('+15551234567', 'xml', null, false);
        $this->assertSame('+15551234567', $result);
    }
}
