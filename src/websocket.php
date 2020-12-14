<?php
/**
 * Pong is a HTML5 game.
 * Copyright (C) 2020 R. V., Diego <diego_pkv@hotmail.com>
 *
 * Pong is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pong is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Pong\Game;
require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(new HttpServer(new WsServer(new Game())), 8080);

$server->run();