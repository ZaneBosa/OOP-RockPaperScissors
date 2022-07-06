<?php

class Weapon
{
    private string $name;
    private array $weakPoints = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWeakPoints(): array
    {
        return $this->weakPoints;
    }

    public function addWeakPoint(Weapon $weapon): void
    {
        $this->weakPoints[] = $weapon;
    }

    public function addWeakPoints(array $weapons): void
    {
        foreach ($weapons as $weapon)
        {
            if (! $weapon instanceof Weapon) continue;
            $this->addWeakPoint($weapon);
        }
    }

    public function isWeakAgainst(Weapon $weapon): bool
    {
        return in_array($weapon, $this->weakPoints);
    }
}
/**
$rock = new Weapon('Rock');
$paper = new Weapon('Paper');
$rock->addWeakPoint($paper);
var_dump($rock->isWeakAgainst($paper));
 **/

class Player
{
    private string $name;
    private Weapon $selection;

    public function __construct(string $name)
    {
        $this->name=$name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSelection(): Weapon
    {
        return $this->selection;
    }

    public function setSelection(Weapon $selection): void
    {
        $this->selection = $selection;
    }
}

class Game
{
    private array $weapons = [];

    private Player $attacker;
    private Player $defender;

    private Weapon $attackerWeapon;
    private Weapon $defenderWeapon;

    private ?Player $winner = null;

    public function __construct(Player $attacker, Player $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;

        $this->setup();
    }

    private function setup(): void
    {
        $this->weapons = [
            $rock = new Weapon('Rock'),
            $paper = new Weapon('Paper'),
            $scissors = new Weapon('Scissors'),
        ];

        $rock->addWeakPoints([$paper]);
        $paper->addWeakPoints([$scissors]);
        $scissors->addWeakPoints([$rock]);
    }

    public function determineResult(): void
    {
        $this->attackerWeapon = $this->attacker->getSelection();
        $this->defenderWeapon = $this->defender->getSelection();

        if ($this->attacker->getSelection() === $this->defender->getSelection()) {
            return;
        }

        if ($this->attacker->getSelection()->isWeakAgainst($this->defender->getSelection())) {
            $this->winner = $this->defender;
            return;
        }
        $this->winner = $this->attacker;
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function getWeapons(): array
    {
        return $this->weapons;
    }

    public function getAttackerWeapon(): Weapon
    {
        return $this->attackerWeapon;
    }

    public function getDefenderWeapon(): Weapon
    {
        return $this->defenderWeapon;
    }

    public function getAttacker(): Player
    {
        return $this->attacker;
    }

    public function getDefender(): Player
    {
        return $this->defender;
    }

    public function isTied(): bool
    {
        return is_null($this->winner);
    }
}

class GameSet
{
    private Player $attacker;
    private Player $defender;

    private Player $winner;

    private const MAX_WINS = 2;

    private int $attackerPoints = 0;
    private int $defenderPoints = 0;

    private array $games = [];

    public function __construct(Player $attacker, Player $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;
    }

    public function determineResult(): void
    {
        while ($this->attackerPoints < self::MAX_WINS && $this->defenderPoints < self::MAX_WINS)
        {
            $game = new Game($this->attacker, $this->defender);
            $this->games[] = $game;

            $weapons = $game->getWeapons();

            $attackerSelectedWeapon = array_rand($weapons);
            $defenderSelectedWeapon = array_rand($weapons);

            $this->attacker->setSelection($weapons[$attackerSelectedWeapon]);
            $this->defender->setSelection($weapons[$defenderSelectedWeapon]);

            $game->determineResult();

            if ($game->isTied()) continue;

            if ($game->getWinner() === $this->attacker) {
                $this->attackerPoints++;
            }

            if ($game->getWinner() === $this->defender) {
                $this->defenderPoints++;
            }
        }
        if ($this->attackerPoints > $this->defenderPoints) {
            $this->winner = $this->attacker;
            return;
        }
        $this->winner = $this->defender;

    }

    public function getWinner(): Player
    {
        return $this->winner;
    }

    public function getAttacker(): Player
    {
        return $this->attacker;
    }

    public function getDefender(): Player
    {
        return $this->defender;
    }

    public function getAttackerPoints(): int
    {
        return $this->attackerPoints;
    }

    public function getDefenderPoints(): int
    {
        return $this->defenderPoints;
    }

    public function getGames(): array
    {
        return $this->games;
    }
}

$game1 = new GameSet(new Player(readline('Enter your player name: ')), new Player('Robert'));
$game1->determineResult();

$game2 = new GameSet(new Player('Bob'), new Player('Jacob'));
$game2->determineResult();

$game3 = new GameSet(new Player('Lucy'), new Player('Sam'));
$game3->determineResult();

$game4 = new GameSet(new Player('Jason'), new Player('Julie'));
$game4->determineResult();

$game5 = new GameSet($game1->getWinner(), $game2->getWinner());
$game5->determineResult();

$game6 = new GameSet($game3->getWinner(), $game4->getWinner());
$game6->determineResult();

$game7 = new GameSet($game5->getWinner(), $game6->getWinner());
$game7->determineResult();

echo "***** QUARTER-FINAL *****" . PHP_EOL;
echo "-------------------------" . PHP_EOL;

echo "{$game1->getAttacker()->getName()} ({$game1->getAttackerPoints()}) : ({$game1->getDefenderPoints()}) {$game1->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game1->getWinner()->getName() . PHP_EOL;
foreach ($game1->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game1->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game1->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;

echo "{$game2->getAttacker()->getName()} ({$game2->getAttackerPoints()}) : ({$game2->getDefenderPoints()}) {$game2->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game2->getWinner()->getName() . PHP_EOL;
foreach ($game2->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game2->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game2->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;

echo "{$game3->getAttacker()->getName()} ({$game3->getAttackerPoints()}) : ({$game3->getDefenderPoints()}) {$game3->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game3->getWinner()->getName() . PHP_EOL;
foreach ($game3->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game3->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game3->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;

echo "{$game4->getAttacker()->getName()} ({$game4->getAttackerPoints()}) : ({$game4->getDefenderPoints()}) {$game4->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game4->getWinner()->getName() . PHP_EOL;
foreach ($game4->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game4->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game4->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;

echo "***** SEMI-FINAL *****" . PHP_EOL;
echo "----------------------" . PHP_EOL;

echo "{$game5->getAttacker()->getName()} ({$game5->getAttackerPoints()}) : ({$game5->getDefenderPoints()}) {$game5->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game5->getWinner()->getName() . PHP_EOL;
foreach ($game5->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game5->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game5->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;

echo "{$game6->getAttacker()->getName()} ({$game6->getAttackerPoints()}) : ({$game6->getDefenderPoints()}) {$game6->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game6->getWinner()->getName() . PHP_EOL;
foreach ($game6->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game6->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game6->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;

echo "***** FINAL *****" . PHP_EOL;
echo "-----------------" . PHP_EOL;

echo "{$game7->getAttacker()->getName()} ({$game7->getAttackerPoints()}) : ({$game7->getDefenderPoints()}) {$game7->getDefender()->getName()}" . PHP_EOL;
echo "Winner: " . $game7->getWinner()->getName() . PHP_EOL;
foreach ($game7->getGames() as $key => $game) {
    $index = $key + 1;
    echo "[Game #{$index}] {$game7->getAttacker()->getName()} had {$game->getAttackerWeapon()->getName()} : {$game7->getDefender()->getName()} had {$game->getDefenderWeapon()->getName()}" . PHP_EOL;
}
echo PHP_EOL;
