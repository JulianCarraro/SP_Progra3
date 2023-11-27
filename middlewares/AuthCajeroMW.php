<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthCajeroMW
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $data = AutentificadorJWT::ObtenerData($token);

        if($data->rol == "Cajero")
        {
            $response = $handler->handle($request);
            $nuevoLog = new LogUsuario();
            $nuevoLog->fechaYHora = date('Y-m-d H:i:s');
            $nuevoLog->idUsuario = $data->idUsuario;
            $nuevoLog->mail = $data->mail;
            $nuevoLog->tipoDeOperacion = "Movimiento Cajero";
            $nuevoLog->CrearLog();
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array("Error" => "No estas habilitado para realizar esta accion"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');

    }
}

?>