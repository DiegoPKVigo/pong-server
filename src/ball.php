<?php
/**
 * Copyright (C) 2020 R. V., Diego <diego_pkv@hotmail.com>
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Pong;

class Ball {
    private array $position;
    private int $radius;
    private float $speedX;
    private float $speedY;
    private const MAX_X_SPEED = 9;
    private const MAX_Y_SPEED = 2;

    public function __construct($gameArea) {
        $this->radius = 10; 
        $this->position = ["x" => $gameArea["x"] / 2, "y" => $gameArea["y"] / 2];
        $this->speedX = 0;
        $this->speedY = 0;
        
    }

    public function getPosX() {
        return $this->position["x"];
    }

    public function setPosX($posX) {
        $this->position["x"] = $posX;
    }

    public function getPosY() {
        return $this->position["y"];
    }

    public function setPosY($posY) {
        $this->position["y"] = $posY;
    }

    public function getSpeedX() {
        return $this->speedX;
    }

    public function setSpeedX($speedX) {
        $this->speedX = $speedX;
    }

    public function getSpeedY() {
        return $this->speedY;
    }

    public function setSpeedY($speedY) {
        $this->speedY = $speedY;
    } 


	/**
	 * Calcula la próxima posición.
	 * Calcula la posición que ocupará la pelota teniendo en cuenta las colisiones.
	 */
    public function updatePos(Match $match) {
        [$x_limit, $y_limit] = $match->getLimits();
        [$firstPlayer, $secondPlayer] = $match->players;
        
        // Invierte el sentido de la bola al ser tocada por un jugador en el borde frontal
        if (
            ($this->getPosX() - $this->radius <= $firstPlayer->getPosX() + $firstPlayer->getWidth() &&
            $this->getPosX() + $this->radius >= $firstPlayer->getPosX() &&
            $this->getPosY() + $this->radius >= $firstPlayer->getPosY() &&
            $this->getPosY() <= $firstPlayer->getPosY() + $firstPlayer->getHeight()) ||
            ($this->getPosX() + $this->radius >= $secondPlayer->getPosX() &&
            $this->getPosX() - $this->radius <= $secondPlayer->getPosX() + $secondPlayer->getWidth() &&
            $this->getPosY() + $this->radius >= $secondPlayer->getPosY() &&
            $this->getPosY() <= $secondPlayer->getPosY() + $secondPlayer->getHeight())
        ) {
			$this->setSpeedX(-$this->getSpeedX());


            $newValue = mt_rand() / mt_getrandmax() * 3;

            // Borde superior del primer jugador.
            if (
                $this->getPosX() - $this->radius >= $firstPlayer->getPosX() &&
                $this->getPosX() - $this->radius <= $firstPlayer->getPosX() + $firstPlayer->width &&
                $this->getPosY() + $this->radius >= $firstPlayer->getPosY() &&
                $this->getPosY() + $this->radius <= $firstPlayer->getPosY() + $this->radius / 2
            ) {
                $this->setPosY($firstPlayer->getPosY() - $this->radius * 2);
				$this->reboteNegativo($newValue);
            // Borde inferior del primer jugador.
            } else if (
                $this->getPosX() - $this->radius >= $firstPlayer->getPosX() &&
                $this->getPosX() - $this->radius <= $firstPlayer->getPosX() + $firstPlayer->width &&
                $this->getPosY() - $this->radius >= $firstPlayer->getPosY() + $firstPlayer->height &&
                $this->getPosY() + $this->radius >= $firstPlayer->getPosY() + $firstPlayer->height
            ) {
                $this->setPosY($firstPlayer->getPosY() + $firstPlayer->height + $this->radius * 2);
				$this->rebotePositivo($newValue);
            // Borde superior del segundo jugador.
            } else if (
                $this->getPosX() + $this->radius <= $secondPlayer->getPosX() + $secondPlayer->width &&
                $this->getPosX() + $this->radius >= $secondPlayer->getPosX() &&
                $this->getPosY() + $this->radius >= $secondPlayer->getPosY() &&
                $this->getPosY() + $this->radius <= $secondPlayer->getPosY() + $this->radius / 2
            ) {
                $this->setPosY($secondPlayer->getPosY() - $this->radius * 2);
                $this->reboteNegativo($newValue);
            // Borde inferior del segundo jugador.
            } else if (
                $this->getPosX() + $this->radius <= $secondPlayer->getPosX() + $secondPlayer->width &&
                $this->getPosX() + $this->radius >= $secondPlayer->getPosX() &&
                $this->getPosY() - $this->radius >= $secondPlayer->getPosY() + $secondPlayer->height &&
                $this->getPosY() + $this->radius >= $secondPlayer->getPosY() + $secondPlayer->height
            ) {
                $this->setPosY($secondPlayer->getPosY() + $secondPlayer->height + $this->radius * 2);
				$this->rebotePositivo($newValue);
            } else {
                // Fuerza la posición de la bola delante del jugador para evitar errores.
                if (
                    $this->getPosX() - $this->radius <= $firstPlayer->getPosX() + $firstPlayer->width &&
                    $this->getPosX() + $this->radius >= $firstPlayer->getPosX() &&
                    $this->getPosY() + $this->radius >= $firstPlayer->getPosY() &&
                    $this->getPosY() - $this->radius <= $firstPlayer->getPosY() + $firstPlayer->height
                ) {
                    $this->setPosX($firstPlayer->getPosX() + $firstPlayer->width + $this->radius);
                } else {
                    $this->setPosX($secondPlayer->getPosX() - $this->radius);
                }

                // Calcula una nueva trayectoria vertical aleatoria.
				if (mt_rand() / mt_getrandmax() > 0.45) {
                    $this->setSpeedY($this->getSpeedY() + $newValue < self::MAX_Y_SPEED ? $this->getSpeedY() + $newValue : $this->getSpeedY() - $newValue);
                } else {
                    $this->setSpeedY($this->getSpeedY() - $newValue > self::MAX_Y_SPEED * -1 ? $this->getSpeedY() - $newValue : $this->getSpeedY() + $newValue);
                }
            }
        }

        // Detiene la bola al tocar cualquiera de los límites laterales de la pantalla.
		if ($this->getPosX() < $this->radius || $this->getPosX() > $match->getLimits()["x"]) {
            $this->setSpeedX(0);
			$this->setSpeedY(0);

			if ($this->getPosX() <= $this->radius) {
				$this->setPosX($this->radius);
				$secondPlayer->addScore();
			} else if ($this->getPosX() >= $x_limit) {
				$this->setPosX($x_limit);
				$firstPlayer->addScore();
			}
		} else {
            $speedX = $this->getSpeedX() < self::MAX_X_SPEED ? $this->getSpeedX() * (1 + Config::getInstance()->getDifficulty / 10000) :  $this->getSpeedX() * 1;
			$this->setSpeedX($speedX);
            $this->setPosX($this->getPosX() + $this->getSpeedX());
            
		}

		// Invierte el sentido de la bola al tocar los bordes superior/inferior de la pantalla.
		if ($this->getPosY() <= $this->radius) {
			$this->setPosY($this->radius);
			$this->setSpeedY(-$this->getSpeedY());
		} else if ($this->getSpeedY() >= $y_limit) {
			$this->setPosY($y_limit - $this->radius);
			$this->setSpeedY(-$this->getSpeedY());
		}

		$this->setPosY($this->getPosY() + $this->getSpeedY());
    }
    
    /**
     * Asegura un rebote vertical negativo (hacia arriba)
     */
    private function reboteNegativo($newValue) {
        $this->speedY = 0;
        $this->speedY = $this->speedY - $newValue > self::MAX_Y_SPEED * -1 ? $this->speedY - $newValue : $this->speedY - mt_rand() / mt_getrandmax() * 2;
    }

    /**
     * Asegura un rebote vertical positivo (hacia abajo)
     */
    private function rebotePositivo($newValue) {
        $this->speedY = 0;
        $this->speedY = $this->speedY + $newValue < self::MAX_Y_SPEED ? $this->speedY + $newValue : $this->speedY + mt_rand() / mt_getrandmax() * 2; 
    }
}