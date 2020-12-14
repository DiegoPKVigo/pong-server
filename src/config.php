<?php
/**
 * Copyright (C) 2020 R. V., Diego <diego_pkv@hotmail.com>
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Pong;

/**
 * Maneja la configuración del juego.<br>
 * La configuración debe almacenarse en un archivo config.json en el directorio config.
 * Dado que esta clase sigue el patrón singleton, dispone de métodos estáticos.
 */
class Config {
    private static $instance = null;
    private array $limits;
    private int $difficulty;
    private int $maxScore;

    private function __construct() {
        $file = file_get_contents(__DIR__ . "/config/config.json");
        $json = json_decode($file);
        var_dump($json);

        $this->limits = $json->limits;
        $this->difficulty = $json->difficulty;
        $this->maxScore = $json->maxScore;
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public function getLimits() {
        return $this->limits;
    }

    public function getDifficulty() {
        return $this->difficulty;
    }

    public function getMaxScore() {
        return $this->maxScore;
    }
}