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

class PuzzleDay3 extends Puzzle
{
    /**
     * @param list<string> $inputs
     */
    protected function partOne(array $inputs): int
    {
        return $this->compute(implode("\n", $inputs));
    }

    /**
     * @param list<string> $inputs
     */
    protected function partTwo(array $inputs): int
    {
        //~ Remove all don't()...do() blocks (on multi-line)
        $dump = \preg_replace("`don't\(\).+?do\(\)`s", '', implode("\n", $inputs));

        //~ Then compute the dump without don't()... blocks
        return $this->compute((string) $dump);
    }

    protected function compute(string $dump): int
    {
        \preg_match_all('`mul\((\d{1,3}),(\d{1,3})\)`', $dump, $matches);

        $result = 0;
        foreach ($matches[0] as $index => $match) {
            $result += (int) $matches[1][$index] * (int) $matches[2][$index];
        }

        return $result;
    }
}
