<?php

class Usuario
{
    public $idUsuario;
    public $mail;
    public $clave;
    public $rol;

    public function __construct()
    {
        
    }

    public function CrearUsuario()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("INSERT INTO usuarios (mail, clave, rol) 
        VALUES (:mail, :clave, :rol)");
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);

        $consulta->execute();
    }

    public static function VerificarSiExisteUsuario($mail, $clave)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT * FROM usuarios where mail = :mail AND clave = :clave");

        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}



?>