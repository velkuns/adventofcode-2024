<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Day;

use Application\Puzzle;

class PuzzleDay1 extends Puzzle
{
    /**
     * @param list<string> $inputs
     */
    protected function partOne(array $inputs): int
    {
        $inputs = \array_map(fn(string $row) => explode('   ', $row), $inputs);
        $a = \array_column($inputs, 0);
        $b = \array_column($inputs, 1);

        sort($a);
        sort($b);

        $sum = 0;
        foreach ($a as $key => $value) {
            $diff = abs((int) $value - (int) $b[$key]);
            $sum += $diff;
        }

        return $sum;
    }

    /**
     * @param list<string> $inputs
     */
    protected function partTwo(array $inputs): int
    {
        $inputs = \array_map(fn(string $row) => explode('   ', $row), $inputs);
        $a = \array_column($inputs, 0);
        $b = \array_count_values(\array_column($inputs, 1));

        $sum = 0;
        foreach ($a as $value) {
            $score = (int) $value * ($b[(string) $value] ?? 0);
            $sum += $score;
        }

        return $sum;
    }
}
