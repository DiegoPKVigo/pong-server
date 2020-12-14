<?php
/**
 * Copyright (C) 2020 R. V., Diego <diego_pkv@hotmail.com>
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Pong;
use Ratchet\ConnectionInterface;

/**
 * Representa al jugador.
 * La instancia de este objeto representa una raqueta de ping pong que será controlada por el jugador.
 */
class Player {
    private ConnectionInterface $connection;
    private int $id;
    private int $posX;
    private int $posY;
    private int $width;
    private int $height;
    private int $score;
    private int $lastInput;

    public function __construct(ConnectionInterface $conn, int $id) {
        $this->connection = $conn;
        $this->id = $id;
        $this->width = 10;
        $this->height = 100;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getId() {
        return $this->id;
    }

    public function getPosX() {
        return $this->posX;
    }

    public function setPosX($posX) {
        $this->posX = $posX;
    }

    public function getPosY() {
        return $this->posY;
    }

    public function setPosY($posY) {
        $this->posY = $posY;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getScore() {
        return $this->score;
    }

    public function addScore() {
        $this->score++;
    }

    public function getLastInput() {
        return $this->lastInput;
    }

    public function setLastInput($input) {
        $this->lastInput = $input;
    }

    public function updatePos() {
        if ($this->y + $this->lastInput > 0 && $this->y +$this->lastInput < Config::getInstance()->getLimits()["y"]) {
            $this->y = $this->y + $this->lastInput;
        }
    }
}