<?php

Class LogUsuario
{
    public $nroDeOperacion;
    public $fechaYHora;
    public $idUsuario;
    public $mail;
    public $tipoDeOperacion;

    public function __construct()
    {
        
    }

    public function CrearLog()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("INSERT INTO logs (fechaYHora, idUsuario, mail, tipoDeOperacion) 
        VALUES (:mail, :clave, :rol, :fechaModificacion)");
        $consulta->bindValue(':fechaYHora', $this->fechaYHora, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeOperacion', $this->tipoDeOperacion, PDO::PARAM_STR);

        $consulta->execute();
    }
}

?>