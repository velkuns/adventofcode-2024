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

class PuzzleDay4 extends Puzzle
{
    /**
     * @param list<string> $inputs
     */
    protected function partOne(array $inputs): int
    {
        $map   = \array_map(\str_split(...), $inputs);
        $count = 0;

        for ($y = 0, $maxY = \count($map); $y < $maxY; $y++) {
            for ($x = 0, $maxX = \count($map[$y]); $x < $maxX; $x++) {
                if ($map[$y][$x] !== 'X') {
                    continue;
                }

                $count += $x < $maxX - 3 && $this->checkLTR($map, $x, $y) ? 1 : 0;
                $count += $x > 2  && $this->checkRTL($map, $x, $y) ? 1 : 0;
                $count += $y < $maxY - 3 && $this->checkTTB($map, $x, $y) ? 1 : 0;
                $count += $y > 2 && $this->checkBTT($map, $x, $y) ? 1 : 0;

                $count +=  $x > 2 && $y > 2 && $this->checkRTLBTT($map, $x, $y) ? 1 : 0;
                $count +=  $x < $maxX - 3 && $y > 2 && $this->checkLTRBTT($map, $x, $y) ? 1 : 0;
                $count +=  $x > 2 && $y < $maxY - 3 && $this->checkRTLTTB($map, $x, $y) ? 1 : 0;
                $count +=  $x < $maxX - 3 && $y < $maxY - 3 && $this->checkLTRTTB($map, $x, $y) ? 1 : 0;
            }
        }

        return $count;
    }

    /**
     * @param list<string> $inputs
     */
    protected function partTwo(array $inputs): int
    {
        $map   = \array_map(\str_split(...), $inputs);
        $count = 0;

        for ($y = 1, $maxY = \count($map); $y < $maxY - 1; $y++) {
            for ($x = 1, $maxX = \count($map[$y]); $x < $maxX - 1; $x++) {
                if ($map[$y][$x] !== 'A') {
                    continue;
                }

                $count += $this->checkXMas($map, $x, $y) ? 1 : 0;
            }
        }

        return $count;
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkRTL(array $map, int $x, int $y): bool
    {
        return $map[$y][$x - 1] === 'M'
            && $map[$y][$x - 2] === 'A'
            && $map[$y][$x - 3] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkLTR(array $map, int $x, int $y): bool
    {
        return $map[$y][$x + 1] === 'M'
            && $map[$y][$x + 2] === 'A'
            && $map[$y][$x + 3] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkBTT(array $map, int $x, int $y): bool
    {
        return $map[$y - 1][$x] === 'M'
            && $map[$y - 2][$x] === 'A'
            && $map[$y - 3][$x] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkTTB(array $map, int $x, int $y): bool
    {
        return $map[$y + 1][$x] === 'M'
            && $map[$y + 2][$x] === 'A'
            && $map[$y + 3][$x] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkRTLBTT(array $map, int $x, int $y): bool
    {
        return $map[$y - 1][$x - 1] === 'M'
            && $map[$y - 2][$x - 2] === 'A'
            && $map[$y - 3][$x - 3] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkLTRBTT(array $map, int $x, int $y): bool
    {
        return $map[$y - 1][$x + 1] === 'M'
            && $map[$y - 2][$x + 2] === 'A'
            && $map[$y - 3][$x + 3] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkRTLTTB(array $map, int $x, int $y): bool
    {
        return $map[$y + 1][$x - 1] === 'M'
            && $map[$y + 2][$x - 2] === 'A'
            && $map[$y + 3][$x - 3] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkLTRTTB(array $map, int $x, int $y): bool
    {
        return $map[$y + 1][$x + 1] === 'M'
            && $map[$y + 2][$x + 2] === 'A'
            && $map[$y + 3][$x + 3] === 'S';
    }

    /**
     * @param list<list<string>> $map
     */
    protected function checkXMas(array $map, int $x, int $y): bool
    {
        $top    = $map[$y - 1][$x - 1] === 'M' && $map[$y - 1][$x + 1] === 'M' && $map[$y + 1][$x - 1] === 'S' && $map[$y + 1][$x + 1] === 'S';
        $bottom = $map[$y + 1][$x - 1] === 'M' && $map[$y + 1][$x + 1] === 'M' && $map[$y - 1][$x - 1] === 'S' && $map[$y - 1][$x + 1] === 'S';
        $left   = $map[$y - 1][$x - 1] === 'M' && $map[$y + 1][$x - 1] === 'M' && $map[$y - 1][$x + 1] === 'S' && $map[$y + 1][$x + 1] === 'S';
        $right  = $map[$y - 1][$x + 1] === 'M' && $map[$y + 1][$x + 1] === 'M' && $map[$y - 1][$x - 1] === 'S' && $map[$y + 1][$x - 1] === 'S';

        return $top || $bottom || $left || $right;
    }
}
