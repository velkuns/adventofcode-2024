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
use Eureka\Component\Console\Output\StreamOutput;
use Eureka\Component\Console\Terminal\Terminal;
use Velkuns\Math\_2D\Direction;
use Velkuns\Math\_2D\Point2D;
use Velkuns\Math\_2D\Vector2DDir;
use Velkuns\Math\Matrix;

class PuzzleDay6 extends Puzzle
{
    /**
     * @param list<string> $inputs
     */
    protected function partOne(array $inputs): int
    {
        $inputs = \array_map(fn($input) => \str_split($input), $inputs);
        $map    = (new Matrix($inputs))->transpose();

        $direction = Direction::Up;
        $position  = $map->locate($direction->value);
        $visited   = 1;

        do {
            $nextPosition = $position->translate(Vector2DDir::fromDirection($direction, invertY: true));

            if ($nextPosition->getX() < 0 || $nextPosition->getY() < 0 || $nextPosition->getX() > $map->width() || $nextPosition->getY() > $map->height()) {
                break;
            }

            $cell = $map->get($nextPosition);
            if ($cell !== '#') {
                if ($cell === '.') {
                    $visited++;
                }
                $position = $nextPosition;
                $map->set($position, 'X');
                //$this->renderMap($map);
                usleep(10_000);
                continue;
            }

            $direction = match ($direction) {
                Direction::Up    => Direction::Right,
                Direction::Right => Direction::Down,
                Direction::Down  => Direction::Left,
                Direction::Left  => Direction::Up,
            };
        } while (true);

        return $visited;
    }

    protected function renderMap(Matrix $map): void
    {
        $buffer = '';
        for ($y = 0; $y < $map->height(); $y++) {
            for ($x = 0; $x < $map->width(); $x++) {
                $buffer .= $map->get(new Point2D($x, $y));
            }
            $buffer .= "\n";
        }

        $terminal = new Terminal(new StreamOutput(\STDOUT, false));
        $terminal->clear();
        $terminal->cursor()->to(1, 1);
        echo $buffer;
    }

    /**
     * @param list<string> $inputs
     */
    protected function partTwo(array $inputs): int
    {
        return 0;
    }
}
