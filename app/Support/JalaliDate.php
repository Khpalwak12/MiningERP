<?php

namespace App\Support;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class JalaliDate
{
    public static function toGregorian(string $shamsiDate): Carbon
    {
        $parts = self::parseParts($shamsiDate);

        return Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/%02d', $parts[0], $parts[1], $parts[2]))->toCarbon();
    }

    public static function fromGregorian(Carbon|string|null $date, string $format = 'Y/m/d'): ?string
    {
        if ($date === null) {
            return null;
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        return Jalalian::fromCarbon($carbon)->format($format);
    }

    public static function today(): string
    {
        return self::fromGregorian(Carbon::today());
    }

    public static function parseParts(string $shamsiDate): array
    {
        $normalized = str_replace('-', '/', trim($shamsiDate));
        $parts = array_map('intval', explode('/', $normalized));

        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid Shamsi date format. Expected YYYY/MM/DD.');
        }

        return $parts;
    }

    public static function monthRange(string $shamsiYearMonth): array
    {
        [$year, $month] = array_map('intval', explode('/', str_replace('-', '/', $shamsiYearMonth)));

        $start = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $year, $month));
        $daysInMonth = $start->getMonthDays();
        $end = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/%02d', $year, $month, $daysInMonth));

        return [$start->toCarbon()->startOfDay(), $end->toCarbon()->endOfDay()];
    }

    public static function yearRange(int $shamsiYear): array
    {
        $start = Jalalian::fromFormat('Y/m/d', sprintf('%04d/01/01', $shamsiYear));
        $end = Jalalian::fromFormat('Y/m/d', sprintf('%04d/12/29', $shamsiYear));

        return [$start->toCarbon()->startOfDay(), $end->toCarbon()->endOfDay()];
    }

    /**
     * Full Shamsi months elapsed from joining date to the current date (exclusive of partial months).
     * Example: 1405/01/01 → 1405/05/01 = 4 months.
     */
    public static function monthsWorkedSince(string $joiningDateShamsi, string $currentDateShamsi): int
    {
        [$joinYear, $joinMonth, $joinDay] = self::parseParts($joiningDateShamsi);
        [$currentYear, $currentMonth, $currentDay] = self::parseParts($currentDateShamsi);

        if ($currentYear < $joinYear
            || ($currentYear === $joinYear && $currentMonth < $joinMonth)
            || ($currentYear === $joinYear && $currentMonth === $joinMonth && $currentDay < $joinDay)) {
            return 0;
        }

        return max(0, ($currentYear * 12 + $currentMonth) - ($joinYear * 12 + $joinMonth));
    }
}
