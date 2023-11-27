<?php

include_once("deposito.php");
include_once("retiro.php");
class Ajuste
{
    public $idAjuste;
    public $motivo;
    public $monto;
    public $idDeposito;
    public $idRetiro;

    public function __construct()
    {
    }

    public function CrearAjuste()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("INSERT INTO ajustes (motivo, monto, idDeposito, idRetiro) 
        VALUES (:motivo, :monto, :idDeposito, :idRetiro)");

        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->bindValue(':idDeposito', $this->idDeposito, PDO::PARAM_INT);
        $consulta->bindValue(':idRetiro', $this->idRetiro, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ObtenerTodos()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT * FROM ajustes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Ajuste');
    }

    static function VerificarSiElAjusteExiste($tipoDeAjuste, $id)
    {
        $ajustes = Ajuste::ObtenerTodos();
        $retorno = 0;

        if($ajustes != null)
        {
            foreach($ajustes as $value)
            {
                if($tipoDeAjuste == "Extraccion")
                {
                    if($value->idRetiro == $id)
                    {
                        $retorno = true;
                    }
                }
                else if($tipoDeAjuste == "Deposito")
                {
                    if($value->idDeposito == $id)
                    {
                        $retorno = true;
                    }
                }
            }
        }

        return $retorno;
    }

}

?>