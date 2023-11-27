<?php

require_once './models/usuario.php';

class UsuarioController extends Usuario
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $mail = $params['mail'];
        $clave = $params['clave'];
        $rol = $params['rol'];
    
        $newUser = new Usuario();
        $newUser->mail = $mail;
        $newUser->clave = $clave;
        $newUser->rol = $rol;
        $newUser->CrearUsuario();
    
        $payload = json_encode(array("mensaje" => "El usuario se ha creado exitosamente"));
    
        $response->getBody()->write($payload);
    
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function LoginUsuario($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $mail = $params['mail'];
        $clave = $params['clave'];

        $usuario = Usuario::VerificarSiExisteUsuario($mail, $clave);

        if($usuario)
        {
            $datos = array('idUsuario' => $usuario->idUsuario, 'mail' => $usuario->mail, 'rol' => $usuario->rol);
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));
        }
        else
        {
            $payload = json_encode(array("Error" => "El usuario o la clave es incorrecta"));
        }
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
        
    }

}

?>