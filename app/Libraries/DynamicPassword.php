<?php

namespace App\Libraries;

use DateTime;
use Exception;

class DynamicPassword
{
    protected array $tokens = [];

    public function __construct(?DateTime $datetime = null)
    {
        $now = $datetime ?? new DateTime();
        $month = $now->format('F');
        $day = (int)$now->format('j');
        $hour = (int)$now->format('G');
        $weekdayIndex = (int)$now->format('N');

        $this->tokens = [
            // Month Characters
            'M1'   => substr($month, 0, 1),
            'M1N'  => ord(strtoupper(substr($month, 0, 1))) - 64,
            'M2'   => substr($month, 1, 1),
            'M2N'  => ord(strtoupper(substr($month, 1, 1))) - 64,

            // Hour
            'H'    => $hour,
            'H12'  => $hour % 12 ?: 12,
            'H24'  => $hour,

            // Date/Day
            'DNM'  => $day,
            'DNW'  => $weekdayIndex,

            // Month & Week info
            'MN'   => (int)$now->format('n'), // Month number (1-12)
            'WN'   => (int)$now->format('W'), // ISO Week number

            // Year
            'Y'    => (int)$now->format('Y'),
            'Y2'   => (int)$now->format('y'),

            // Special symbol from weekday (based on keyboard row 1)
            'SW'   => ['!', '@', '#', '$', '%', '^', '&'][$weekdayIndex - 1] ?? '*',
        ];
    }

    public function getTokenList(): array
    {
        return [
            'M1' => 'First letter of month (e.g. J)',
            'M1N' => 'Alphabet position of M1 (e.g. J=10)',
            'M2' => 'Second letter of month',
            'M2N' => 'Alphabet position of M2',
            'H' => 'Hour (0-23)',
            'H12' => 'Hour in 12-hour format',
            'H24' => 'Hour in 24-hour format',
            'DNM' => 'Day number of month (1-31)',
            'DNW' => 'Day number in week (Monday=1 to Sunday=7)',
            'MN' => 'Month number (1-12)',
            'WN' => 'ISO week number',
            'Y' => 'Year (e.g. 2025)',
            'Y2' => 'Last two digits of year',
            'SW' => 'Special symbol based on day of week (! to &)',
        ];
    }

    public function generate(string $pattern): string
    {
        return preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) {
            $expr = $matches[1];
            foreach ($this->tokens as $key => $value) {
                $expr = preg_replace("/\\b$key\\b/", $value, $expr);
            }

            // Evaluate if it's math-only
            if (preg_match('#^[0-9+\-*/().\s]+$#', $expr)) {
                return $this->evaluateMath($expr);
            }

            return $expr;
        }, $pattern);
    }

    public function validate(string $userInput, string $expectedPattern): bool
    {
        return $userInput === $this->generate($expectedPattern);
    }

    protected function evaluateMath(string $expr): string
    {
        if (!preg_match('#^[0-9+\-*/().\s]+$#', $expr)) {
            throw new Exception("Unsafe expression detected.");
        }

        @eval("\$result = ($expr);");
        return (string)($result ?? '');
    }
}
