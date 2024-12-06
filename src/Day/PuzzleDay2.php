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

class PuzzleDay2 extends Puzzle
{
    /**
     * @param list<string> $inputs
     */
    protected function partOne(array $inputs): int
    {
        return count($this->getValidReports($inputs, 0));
    }

    /**
     * @param list<string> $inputs
     */
    protected function partTwo(array $inputs): int
    {
        return count($this->getValidReports($inputs, 1));
    }

    /**
     * @param list<string> $inputs
     * @return list<list<int>>
     */
    protected function getValidReports(array $inputs, int $toleration): array
    {
        $inputs = \array_map(fn(string $row) => array_map(\intval(...), \explode(' ', $row)), $inputs);

        return \array_filter($inputs, fn(array $row) => $this->isSafeReport($row, $toleration));
    }

    /**
     * @param list<int> $row
     */
    protected function isSafeReport(array $row, int $toleration): bool
    {
        $sign  = $row[1] <=> $row[0];
        $sign1 = $row[2] <=> $row[1];

        if ($sign === 0) {
            return $this->retryWithoutBadLevels($row, 0, $toleration);
        }

        if ($sign !== $sign1) {
            $case1 = $this->retryWithoutBadLevels($row, 0, $toleration); // Real inversion, so try between 0 and 1
            $case2 = $this->retryWithoutBadLevels($row, 1, $toleration); // No inversion, so try between 1 and 2
            return $case1 || $case2;
        }

        for ($index = 0, $max = count($row) - 1; $index < $max; $index++) {
            $level      = $row[$index];
            $nextLevel  = $row[$index + 1];

            $diffSign   = ($nextLevel <=> $level) !== $sign;
            $moreThan3  = abs($level - $nextLevel) > 3;
            if ($diffSign && !$this->retryWithoutBadLevels($row, $index, $toleration)) {
                return false;
            }

            if ($moreThan3 && !$this->retryWithoutBadLevels($row, $index, $toleration)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param list<int> $row
     */
    protected function retryWithoutBadLevels(array $row, int $index, int $toleration): bool
    {
        if ($toleration === 0) {
            return false;
        }

        $a = $this->excludeLevel($row, $index);
        $b = $this->excludeLevel($row, $index + 1);
        return $this->isSafeReport($a, $toleration - 1) || $this->isSafeReport($b, $toleration - 1);
    }

    /**
     * @param list<int> $row
     * @return list<int>
     */
    protected function excludeLevel(array $row, int $index): array
    {
        \array_splice($row, $index, 1);

        return $row;
    }
}
