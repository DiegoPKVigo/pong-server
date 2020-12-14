<?php
/**
 * Copyright (C) 2020 R. V., Diego <diego_pkv@hotmail.com>
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Pong;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Clase principal para el servidor de Pong.
 */
class Game implements MessageComponentInterface {
    private const WAITING = 0;
    private const MATCH_START = 1;
    public const PLAYING = 2;
    public const MATCH_END = 3;
    private const CLOSE = 4;
    private $clients;
    private $waiting;
    private $matches;
    private $debug;
    
    /**
     * Inicializa el servidor y permite al cliente interactuar con el servidor. 
     * @param boolean $debug - Muestra los logs si su valor es TRUE.
     */
    public function __construct($debug = false) {
        $this->clients = new \SplObjectStorage;
        $this->waiting = new \SplObjectStorage;
        $this->matches = new \SplObjectStorage;
        $this->debug = $debug;
    }

    /**
     * Almacena la conexión del usuario conectado.
     *
     * @param ConnectionInterface $conn - Se corresponde con la conexión asociada al usuario que se conectó al servidor.
     * @return void
     */
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->log("New connection! ({$conn->resourceId})\n");
    }

    /**
     * Recibe los parámetros enviados por el cliente.
     *
     * @param ConnectionInterface $from - Se corresponde con la conexión asociada al usuario que envió el mensaje.
     * @param [type] $msg - El mensaje en formato JSON recibido del cliente.
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg);
        $this->checkMsg($from, $msg);
    }

    /**
     * Cierra la conexión en caso de que el cliente se desconecte.
     *
     * @param ConnectionInterface $conn - Se corresponde con la conexión del usuario que la cerró.
     * @return void
     */
    public function onClose(ConnectionInterface $conn) {
        if ($this->waiting->contains($conn)) {
            $this->waiting->detach($conn);
        }

        $this->clients->detach($conn);
        $this->log("Connection {$conn->resourceId} has disconnected\n");
    }

    /**
     * Gestiona los errores que sucedan durante la conexión
     *
     * @param ConnectionInterface $conn - Se corresponde con la conexión que generó el error.
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Comprueba el estado del mensaje recibido.
     *
     * @param ConnectionInterface $from - Se corresponde con la conexión asociada al usuario.
     * @param [Object] $msg - El objeto JSON parseado que se debe inspeccionar.
     * @return void
     */
    private function checkMsg(ConnectionInterface $from, $msg) {
        switch($msg) {
            case self::WAITING:
                $this->waiting($from);
                $this->log("({$from->resourceId}) is waiting to play\n");
            break;
            case self::MATCH_START:

            break;
        }
    }

    /**
     * Crea o une a un jugador a una partida.<br>
     * Inicia la partida en caso de que alguien estuviese esperando.
     * En caso de que nadie estuviese esperando, pone al jugador en espera.
     * @param ConnectionInterface $from - Se corresponde con la conexión asociada al usuario.
     * @return void
     */
    private function waiting(ConnectionInterface $from) {
        if ($this->waiting->count() > 0) {
            if ($this->waiting->key() > 0) {
                $this->waiting->rewind();
                $client = $this->waiting->current();

                $match = new Match($client, $from);
                foreach($match->players as $player) {
                    $player->getConnection()->send(
                        json_encode([
                            "status" => self::MATCH_START,
                            "params" => ["matchId"]
                        ])
                    );
                }
                $match->start();
                
                $this->matches->attach($match);

                // Libera los jugadores que ya no están esperando.
                foreach ($match->players as $player) {
                    $this->waiting->detach($player->getConnection());
                }
            }
        } else {
            $this->waiting->attach($from);
            $this->log("Connection ({$from->resourceId}) is waiting to play\n");
        }
    }

    /**
     * Registra información de utilidad y la muestra en la pantalla.
     *
     * @param [type] $message - El mensaje que se debe mostrar.
     * @return void
     */
    private function log($message) {
        if($this->debug)
            echo $message;
    }
}