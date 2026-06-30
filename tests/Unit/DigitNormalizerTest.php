<?php

namespace Tests\Unit;

use App\Support\DigitNormalizer;
use App\Support\JalaliDate;
use PHPUnit\Framework\TestCase;

class DigitNormalizerTest extends TestCase
{
    public function test_converts_persian_and_arabic_digits_to_western(): void
    {
        $this->assertSame('1404/01/15', DigitNormalizer::toWestern('۱۴۰۴/۰۱/۱۵'));
        $this->assertSame('12345.67', DigitNormalizer::toWestern('١٢٣٤٥.٦٧'));
        $this->assertSame('10', DigitNormalizer::toWestern('۱۰'));
    }

    public function test_jalali_date_accepts_persian_digits(): void
    {
        $gregorian = JalaliDate::toGregorian('۱۴۰۴/۰۱/۱۵')->format('Y-m-d');
        $western = JalaliDate::toGregorian('1404/01/15')->format('Y-m-d');

        $this->assertSame($western, $gregorian);
    }
}
