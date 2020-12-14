# Pong server

Pong is a HTML5 game based on the classic pong game.
This project is written in [PHP](https://www.php.net/) and based on the [WebSocket](https://tools.ietf.org/html/rfc6455) protocol over the [Ratchet](http://socketo.me/) library.

## Requirements

In order to be able to deploy Pong you need the [PHP](https://www.php.net/) interpreter.

## Building

Follow the next steps to launch the WebSocket server:

1. Clone the repository
2. Change to the Pong directory:
```cd pong-server```
3. Install the required dependencies:
```composer install```
4. _(Optional)_ Configure the game editing the json file inside the __config__ directory

## Usage

Run the websocket.php file with the php interpreter located in the source directory:
```php websocket.php```
