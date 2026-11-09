<?php

namespace Tests\Unit;

use App\Console\Commands\SanitizeSeoTitles;
use PHPUnit\Framework\TestCase;

class SanitizeSeoTitlesTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('titleProvider')]
    public function test_clean_title(string $input, string $expected): void
    {
        $this->assertSame($expected, SanitizeSeoTitles::cleanTitle($input));
    }

    public static function titleProvider(): array
    {
        return [
            ['Insights & Thought Leadership on Enterprise AI | Kouventa Blog', 'Insights & Thought Leadership on Enterprise AI'],
            ['Blog & Thought Leadership on Enterprise AI | Kouventa', 'Blog & Thought Leadership on Enterprise AI'],
            ['AI Solutions for Enterprise Teams & Departments | Kouventa', 'AI Solutions for Enterprise Teams & Departments'],
            ['Customer Engagement Solutions by Industry & Use Case | Kouventa', 'Customer Engagement Solutions by Industry & Use Case'],
            ['Enterprise AI Pricing & Plans | Kouventa', 'Enterprise AI Pricing & Plans'],
            ['Harga & Paket Enterprise AI | Kouventa', 'Harga & Paket Enterprise AI'],
            ['Customer Engagement Pricing & WhatsApp Calculator | Kouventa', 'Customer Engagement Pricing & WhatsApp Calculator'],
            ['Terms & Conditions | Kouventa by Helios', 'Terms & Conditions'],
            ['Privacy Policy | Kouventa by Helios', 'Privacy Policy'],
            ['About Us - Kouventa', 'About Us'],
            ['Contact Us — Kouventa', 'Contact Us'],
            ['AI Agent Suite | Kouventa Enterprise AI', 'AI Agent Suite'],
            ['Kouventa', 'Kouventa'],
            ['Kouventa AI', 'Kouventa AI'],
            ['Why Kouventa for Enterprise', 'Why Kouventa for Enterprise'],
            ['Pure Clean Title', 'Pure Clean Title'],
        ];
    }
}
