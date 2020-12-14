<?php
/**
 * Copyright (C) 2020 R. V., Diego <diego_pkv@hotmail.com>
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Pong;

use Ratchet\ConnectionInterface;

/**
 * Representa una partida entre dos jugadores.
 */
class Match {
    private static int $nextMatchId = 1;
    private int $matchId;
    private array $limits = Config::getInstance()->getLimits();
    public array $players;
    private Ball $ball;
    
    public function __construct(ConnectionInterface $player1, ConnectionInterface $player2) {
        $this->players = [new Player($player1, 1), new Player ($player2, 2)];
        $this->matchId = self::$nextMatchId;
        self::$nextMatchId++;
    }
    
    
    public function start() {
        $this->ball = new Ball($this->limits);
        foreach($this->players as $player) {
            $player->setPosY($player->getConnection()->send($this->limits["y"] / 2));
        }
    }
    
    public function stop() {
        $this->sendData(Game::MATCH_END);
    }
    
    public function restart() {
        
    }
    
    public function update() {
        $this->ball->updatePos($this);
        foreach($this->players as $player) {
            $player->updatePos();
        }

        $params = ["ball" => [
            "x" => $this->ball->getPosX(),
            "y" => $this->ball->getPosY(),
            "speedX" => $this->ball->getSpeedX(),
            "speedY" => $this->ball->getSpeedY()
        ]];
        // TODO añadir score y params de jugador.


        $this->sendData(Game::PLAYING, $params);
    }
    
    private function sendData($status, $params = null) {
        $data = [
            "status" => $status,
            //"match" => $this->getMatchId(),
            //"ball" => [
            //    "x" => $this->ball->getPosX(),
            //    "y" => $this->ball->getPosY(),
            //    "speedX" => $this->ball->getSpeedX(),
            //    "speedY" => $this->ball->getSpeedY()
            //],
        ];
        if($params !== null) {
            foreach($params as $param) {
                
            }
        }
        [$player1, $player2] = $this->players;
        $player1->getConnection()->send($data["player"] = [
            "y" => $player2->getPosY(),
            "speedY" => $player2->getLastInput()
        ]);
        $player2->getConnection()->send($data["player"] = [
            "y" => $player1->getPosY(),
            "speedY" => $player1->getLastInput() 
        ]);
    }
            
    public function getLimits() {
        return $this->limits;
    }

    private function getMatchId() {
        return $this->matchId;
    }
}