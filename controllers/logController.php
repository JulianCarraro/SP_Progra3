<?php

// require_once './models/log.php';

// class LogController extends LogUsuario
// {
//     public function CargarUno($request, $response, $args)
//     {
//         $header = $request->getHeaderLine('Authorization');
//         $token = trim(explode("Bearer", $header)[1]);

//         $data = AutentificadorJWT::ObtenerData($token);
    
//         $newUser = new Log();
//         $newUser->mail = $data->mail;
//         $newUser->clave = $clave;
//         $newUser->rol = $rol;
//         $newUser->CrearUsuario();
    
//         $payload = json_encode(array("mensaje" => "El usuario se ha creado exitosamente"));
    
//         $response->getBody()->write($payload);
    
//         return $response->withHeader('Content-Type', 'application/json');
//     }

// }

?>