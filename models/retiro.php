<?php

include_once("cuenta.php");
class Retiro{
    
    public $idRetiro;
    public $nroDeCuenta;
    public $moneda;
    public $monto;
    public $fecha;

    public function __construct() 
    {
    }

    public function CrearRetiro()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("INSERT INTO retiros (nroDeCuenta, moneda, monto, fecha) 
        VALUES (:nroDeCuenta, :moneda, :monto, :fecha)");

        $consulta->bindValue(':nroDeCuenta', $this->nroDeCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ObtenerTodos()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT c.nombreYApellido, c.tipoDeDocumento, c.nroDocumento, c.mail, c.tipoDeCuenta, c.saldoInicial, c.nroDeCuenta, 
        c.estado, r.idRetiro, r.moneda, r.monto, r.fecha FROM retiros as r LEFT JOIN cuentas as c ON c.nroDeCuenta = r.nroDeCuenta WHERE c.estado = 'Activo'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ObtenerRetirosDeUsuario($nroDeCuenta)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT c.nombreYApellido, c.tipoDeDocumento, c.nroDocumento, c.mail, c.tipoDeCuenta, c.saldoInicial, c.nroDeCuenta, 
        c.estado, r.idRetiro, r.moneda, r.monto, r.fecha FROM retiros as r LEFT JOIN cuentas as c ON c.nroDeCuenta = r.nroDeCuenta WHERE r.nroDeCuenta = :nroDeCuenta AND c.estado = 'Activo'");

        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    static function TotalRetirado($tipoDeCuenta, $moneda, $fecha)
    {
        $retiros = Retiro::ObtenerTodos();
        $montoTotal = 0;

        foreach($retiros as $value)
        {
            if($value['tipoDeCuenta'] == $tipoDeCuenta . $moneda && $value['moneda'] == $moneda && $value['fecha'] == $fecha)
            {
                $montoTotal += $value['monto'];
            }
        }

        return $montoTotal;
    }

    static function ObtenerRetirosDeCuenta($nroDeCuenta)
    {
        $retiros = Retiro::ObtenerTodos();
        $retirosDeCuenta = NULL;

        foreach($retiros as $value)
        {
            if($value['nroDeCuenta'] == $nroDeCuenta)
            {
                $retirosDeCuenta[] = $value;
            }
        }
        
        return $retirosDeCuenta;
    }

    static function ObtenerRetirosEntreFechas($fechaInicio, $fechaFinal)
    {
        $retiros = Retiro::ObtenerTodos();
        $retirosEntreFechas = NULL;
        $hayRetiro = false;

        foreach($retiros as $value)
        {
            if($value['fecha'] >= $fechaInicio && $value['fecha'] <= $fechaFinal)
            {
                $retirosEntreFechas[] = $value;
                $hayRetiro = true;
            }
        }

        if($hayRetiro)
        {
            usort($retirosEntreFechas, "Retiro::CompararNombre");
        }
        
        return $retirosEntreFechas;
    }

    static function CompararNombre($retiroUno, $retiroDos)
    {
        return strcmp($retiroUno['nombreYApellido'], $retiroDos['nombreYApellido']);
    }
    
    static function ObtenerRetirosPorTipoDeCuenta($tipoDeCuenta)
    {
        $retiros = Retiro::ObtenerTodos();
        $retirosPorTipoDeCuenta = NULL;

        foreach($retiros as $value)
        {
            if($value['tipoDeCuenta'] == $tipoDeCuenta)
            {
                $retirosPorTipoDeCuenta[] = $value;
            }
            else
            {
                $auxTipoDeCuenta = substr($value['tipoDeCuenta'], 0, 2);
                if($auxTipoDeCuenta == $tipoDeCuenta)
                {
                    $retirosPorTipoDeCuenta[] = $value;
                }
            }
        }

        return $retirosPorTipoDeCuenta;
    }
    static function ObtenerRetirosPorMoneda($moneda)
    {
        $retiros = Retiro::ObtenerTodos();
        $retirosPorMoneda = NULL;

        foreach($retiros as $value)
        {
            if($value['moneda'] == $moneda)
            {
                $retirosPorMoneda[] = $value;
            }
        }

        return $retirosPorMoneda;
    }

}

?>