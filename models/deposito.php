<?php

include_once("cuenta.php");
class Deposito{
    
    public $idDeposito;
    public $nroDeCuenta;
    public $urlImagen;
    public $moneda;
    public $monto;
    public $fecha;

    public function __construct() 
    {
    }

    public function CrearDeposito()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("INSERT INTO depositos (nroDeCuenta, urlImagen, moneda, monto, fecha) 
        VALUES (:nroDeCuenta, :urlImagen, :moneda, :monto, :fecha)");

        $consulta->bindValue(':nroDeCuenta', $this->nroDeCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':urlImagen', $this->urlImagen, PDO::PARAM_STR);
        $consulta->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ObtenerTodos()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT c.nombreYApellido, c.tipoDeDocumento, c.nroDocumento, c.mail, c.tipoDeCuenta, c.saldoInicial, c.nroDeCuenta, 
        c.estado, d.idDeposito, d.moneda, d.monto, d.fecha FROM depositos as d LEFT JOIN cuentas as c ON c.nroDeCuenta = d.nroDeCuenta WHERE c.estado = 'Activo'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ObtenerDepositosDeUsuario($nroDeCuenta)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT c.nombreYApellido, c.tipoDeDocumento, c.nroDocumento, c.mail, c.tipoDeCuenta, c.saldoInicial, c.nroDeCuenta, 
        c.estado, d.idDeposito, d.moneda, d.monto, d.fecha FROM depositos as d LEFT JOIN cuentas as c ON c.nroDeCuenta = d.nroDeCuenta WHERE d.nroDeCuenta = :nroDeCuenta AND c.estado = 'Activo'");

        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GuardarImagenDeposito($ruta, $urlImagen, $tipoDeCuenta, $nroDeCuenta)
    {
        $destino = $ruta . $nroDeCuenta . $tipoDeCuenta . ".jpg";

        move_uploaded_file($urlImagen["tmp_name"], $destino);

        return $destino;

    }

    static function TotalDepositado($tipoDeCuenta, $moneda, $fecha)
    {
        $depositos = Deposito::ObtenerTodos();
        $montoTotal = 0;

        foreach($depositos as $value)
        {
            if($value['tipoDeCuenta'] == $tipoDeCuenta .  $moneda && $value['moneda'] == $moneda && $value['fecha'] == $fecha)
            {
                $montoTotal += $value['monto'];
            }
        }

        return $montoTotal;
    }

    static function ObtenerDepositosDeCuenta($nroDeCuenta)
    {
        $depositos = Deposito::ObtenerTodos();
        $depositosDeCuenta = NULL;

        foreach($depositos as $value)
        {
            if($value['nroDeCuenta'] == $nroDeCuenta)
            {
                $depositosDeCuenta[] = $value;
            }
        }
        
        return $depositosDeCuenta;
    }

    static function ObtenerDepositosEntreFechas($fechaInicio, $fechaFinal)
    {
        $depositos = Deposito::ObtenerTodos();
        $depositosEntreFechas = NULL;
        $hayDeposito = false;

        foreach($depositos as $value)
        {
            if($value['fecha'] >= $fechaInicio && $value['fecha'] <= $fechaFinal)
            {
                $depositosEntreFechas[] = $value;
                $hayDeposito = true;
            }
        }

        if($hayDeposito)
        {
            usort($depositosEntreFechas, "Deposito::CompararNombre");
        }
        
        return $depositosEntreFechas;
    }

    static function CompararNombre($depositoUno, $depositoDos)
    {
        return strcmp($depositoUno['nombreYApellido'], $depositoDos['nombreYApellido']);
    }
    
    static function ObtenerDepositosPorTipoDeCuenta($tipoDeCuenta)
    {
        $depositos = Deposito::ObtenerTodos();
        $depositosPorTipoDeCuenta = NULL;

        foreach($depositos as $value)
        {
            if($value['tipoDeCuenta'] == $tipoDeCuenta)
            {
                $depositosPorTipoDeCuenta[] = $value;
            }
            else
            {
                $auxTipoDeCuenta = substr($value['tipoDeCuenta'], 0, 2);
                if($auxTipoDeCuenta == $tipoDeCuenta)
                {
                    $depositosPorTipoDeCuenta[] = $value;
                }
            }
        }

        return $depositosPorTipoDeCuenta;
    }
    static function ObtenerDepositosPorMoneda($moneda)
    {
        $depositos = Deposito::ObtenerTodos();
        $depositosPorMoneda = NULL;

        foreach($depositos as $value)
        {
            if($value['moneda'] == $moneda)
            {
                $depositosPorMoneda[] = $value;
            }
        }

        return $depositosPorMoneda;
    }

}

?>