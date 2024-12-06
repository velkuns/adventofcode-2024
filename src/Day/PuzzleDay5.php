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

class PuzzleDay5 extends Puzzle
{
    /**
     * @param list<string> $inputs
     */
    protected function partOne(array $inputs): int
    {
        ['rules' => $rules, 'updates' => $updates] = $this->parseInput($inputs);

        $result = 0;
        foreach ($updates as $update) {
            for ($p = 0, $max = \count($update) - 1; $p < $max; $p++) {
                if ($this->isPageBreakRules($update[$p], $update[$p + 1], $rules)) {
                    continue 2;
                }
            }

            //~ No page break found
            $result += $this->getPageInMiddle($update);
        }

        return $result;
    }

    /**
     * @param list<string> $inputs
     */
    protected function partTwo(array $inputs): int
    {
        ['rules' => $rules, 'updates' => $updates] = $this->parseInput($inputs);

        $result = 0;
        foreach ($updates as $update) {
            $origin = \implode(' - ', $update);
            $hasBeenFixed = false;
            $max          = \count($update) - 1;
            $position     = 0;

            do {
                if ($this->isPageBreakRules($update[$position], $update[$position + 1], $rules)) {
                    $update       = $this->swapPages($update, $position, $rules);
                    $hasBeenFixed = true;
                    $position += $position > 0 ? -1 : 0;
                } else {
                    $position++;
                }

            } while ($position < $max);

            if ($hasBeenFixed) {
                $result += $this->getPageInMiddle($update);
            }
        }

        return $result;
    }

    /**
     * @param list<int> $update
     * @param array<int, array<int, bool>> $rules
     * @return list<int>
     * Too low: 4407
     */
    protected function swapPages(array $update, int $position, array $rules): array
    {
        $page = $update[$position];
        $next = $update[$position + 1];
        $update[$position]     = $next;
        $update[$position + 1] = $page;

        return $update;
    }

    /**
     * @param array<int, array<int, bool>> $rules
     */
    protected function isPageBreakRules(int $page, int $nextPage, array $rules): bool
    {
        return $rules[$nextPage][$page] ?? false;
    }

    /**
     * @param list<int> $update
     */
    protected function getPageInMiddle(array $update): int
    {
        $mid  = ((\count($update) - 1) / 2);
        $page =  $update[$mid];

        //echo "page in middle : $page" . PHP_EOL;

        return $page;
    }

    /**
     * @param list<string> $inputs
     * @return array{rules: array<int, array<int, bool>>, updates: list<list<int>>}
     */
    protected function parseInput(array $inputs): array
    {
        $data = ['rules' => [], 'updates' => []];

        foreach ($inputs as $line) {
            if (\str_contains($line, '|')) {
                $data['rules'][]   = \array_map(intval(...), \explode('|', $line));
            } elseif (\str_contains($line, ',')) {
                $data['updates'][] = \array_map(intval(...), \explode(',', $line));
            }
        }

        $rules = [];
        foreach ($data['rules'] as [$first, $second]) {
            $rules[$first][$second] = true;
        }
        $data['rules'] = $rules;

        return $data;
    }
}
