<?php

class Cuenta
{
    public $nombreYApellido;
    public $tipoDeDocumento;
    public $nroDocumento;
    public $mail;
    public $tipoDeCuenta;
    public $moneda;
    public $saldoInicial;
    public $nroDeCuenta;
    public $urlImagen;
    public $estado;

    public function __construct()
    {
    }

    public function CrearCuenta()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("INSERT INTO cuentas (nombreYApellido, tipoDeDocumento, nroDocumento, mail, tipoDeCuenta, 
        moneda, saldoInicial, nroDeCuenta, urlImagen, estado) 
        VALUES (:nombreYApellido, :tipoDeDocumento, :nroDocumento, :mail, :tipoDeCuenta, :moneda, :saldoInicial, :nroDeCuenta, :urlImagen, :estado)");

        $consulta->bindValue(':nombreYApellido', $this->nombreYApellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeDocumento', $this->tipoDeDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':nroDocumento', $this->nroDocumento, PDO::PARAM_INT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeCuenta', $this->tipoDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
        $consulta->bindValue(':saldoInicial', $this->saldoInicial, PDO::PARAM_INT);
        $consulta->bindValue(':urlImagen', $this->urlImagen, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->execute();
    }
    
    public static function ObtenerTodos()
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT * FROM cuentas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'cuenta');
    }

    public static function ObtenerCuenta($nroDeCuenta)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("SELECT * FROM cuentas WHERE nroDeCuenta = :nroDeCuenta");

        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_INT);
        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'cuenta');
    }

    public static function ModificarCuenta($nombreYApellido, $tipoDeDocumento, $nroDocumento, $mail, $tipoDeCuenta, $moneda, $nroDeCuenta)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("UPDATE cuenta set nombreYApellido = :nombreYApellido, tipoDeDocumento = :tipoDeDocumento, nroDocumento = :nroDocumento, 
        mail = :mail, tipoDeCuenta = :tipoDeCuenta, moneda = :moneda WHERE nroDeCuenta = :nroDeCuenta AND estado = 'Activo'");


        $consulta->bindValue(':nombreYApellido', $nombreYApellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeDocumento', $tipoDeDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':nroDocumento', $nroDocumento, PDO::PARAM_INT);
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeCuenta', $tipoDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':moneda', $moneda, PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->rowCount(); //retorna la cantidad de filas afectadas
    }

    public function ModificarSaldoCuenta($nroDeCuenta, $tipoDeCuenta, $monto)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("UPDATE cuenta set monto = :monto WHERE nroDeCuenta = :nroDeCuenta AND tipoDeCuenta = :tipoDeCuenta AND estado = 'Activo'");


        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeCuenta', $tipoDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $monto, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->rowCount(); //retorna la cantidad de filas afectadas
    }

    public static function BorrarCuenta($nroDeCuenta, $tipoDeCuenta, $estado)
    {
        $objAcessoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAcessoDatos->PrepararConsulta("UPDATE cuenta set estado = :estado WHERE nroDeCuenta = :nroDeCuenta AND tipoDeCuenta = :tipoDeCuenta");


        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeCuenta', $tipoDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->rowCount(); //retorna la cantidad de filas afectadas
    }
    public static function ObtenerNroDeCuenta($nroDeDocumento)
    {
        $cuentas = Cuenta::ObtenerTodos();
        $nroDeCuenta = null;

        foreach($cuentas as $cuenta)
        {
            if($cuenta->nroDocumento = $nroDeDocumento)
            {
                $nroDeCuenta = $cuenta->nroDeCuenta;
                break;
            }
        }

        return $nroDeCuenta;
    }

    public static function ObtenerSaldoCuenta($nroDeCuenta)
    {
        $cuentas = Cuenta::ObtenerTodos();
        $saldoCuenta = 0;

        foreach($cuentas as $cuenta)
        {
            if($cuenta->nroDeCuenta = $nroDeCuenta)
            {
                $saldoCuenta = $cuenta->saldoInicial;
                break;
            }
        }

        return $saldoCuenta;
    }

    public static function ObtenerImagen($nroDeCuenta)
    {
        $cuentas = Cuenta::ObtenerTodos();
        $urlImagen = 0;

        foreach($cuentas as $cuenta)
        {
            if($cuenta->nroDeCuenta = $nroDeCuenta)
            {
                $urlImagen = $cuenta->urlImagen;
                break;
            }
        }

        return $urlImagen;
    }

    public function GuardarImagenCuenta($ruta, $urlImagen, $nroDeCuenta, $tipoDeCuenta)
    {
        $destino = $ruta . $nroDeCuenta . $tipoDeCuenta . ".jpg";

        move_uploaded_file($urlImagen["tmp_name"], $destino);

        return $destino;
    }

    static function ExisteCuentaPorNroDeCuenta($nroDeCuenta)
    {
        $existe = false;
        $cuentas = Cuenta::ObtenerTodos();

        if($cuentas != null)
        {
            foreach($cuentas as $value)
            {
                if($value->nroDeCuenta == $nroDeCuenta)
                {
                    $existe = true;
                    break;
                }
            }
        }

        return $existe;
    }


    static function ExisteCuentaPorNroYTipo($nroDeCuenta, $tipoDeCuenta)
    {
        $retorno = "No existe la combinacion de nro y tipo de cuenta <br>";
        $cuentas = Cuenta::ObtenerTodos();

        if($cuentas != null)
        {
            foreach($cuentas as $value)
            {
                if($value->nroDeCuenta == $nroDeCuenta)
                {
                    if($value->tipoDeCuenta == $tipoDeCuenta)
                    {
                        $retorno = $value;
                        break;
                    }
                    else
                    {
                        $retorno = "Tipo de cuenta incorrecto <br>";
                        break;
                    }
                }
            }
        }

        return $retorno;
    }

    static function ConsultarCuentaPorNroYTipo($nroDeCuenta, $tipoDeCuenta)
    {
        $cuentas = Cuenta::ObtenerTodos();

        if($cuentas != null)
        {
            $dato = Cuenta::ExisteCuentaPorNroYTipo($nroDeCuenta, $tipoDeCuenta);
            if(!is_string($dato))
            {
                $retorno = "La moneda de la cuenta es: " . $dato->_moneda . " y el saldo es: " . $dato->_saldoInicial;
            }
            else
            {
                $retorno = $dato;
            }
        }

        return $retorno;
    }


}

?>