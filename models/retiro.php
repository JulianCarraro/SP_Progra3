<?php

include_once("cuenta.php");

class Retiro
{
    public $_nombreYApellido;
    public $_tipoDeDocumento;
    public $_nroDocumento;
    public $_mail;
    public $_tipoDeCuenta;
    public $_moneda;
    public $_saldoInicial;
    public $_nroDeCuenta;
    public $_urlImagen;
    public $_monto;
    public $_fecha;
    public $_idRetiro;

    public function __construct($nombreYApellido, $tipoDeDocumento, $nroDocumento, $mail, $tipoDeCuenta, $moneda, $saldoInicial, $nroDeCuenta, $urlImagen, $monto, $idRetiro) 
    {
        $this->_nombreYApellido = $nombreYApellido;
        $this->_tipoDeDocumento = $tipoDeDocumento;
        $this->_nroDocumento = $nroDocumento;
        $this->_mail = $mail;
        $this->_tipoDeCuenta = $tipoDeCuenta;
        $this->_moneda = $moneda;
        $this->_saldoInicial = $saldoInicial;
        $this->_nroDeCuenta = $nroDeCuenta;
        $this->_urlImagen = $urlImagen;
        $this->_monto = $monto;
        $this->_idRetiro = $idRetiro;
        $fechaAux = new DateTime();
        $this->_fecha = $fechaAux->format("d-m-Y");
    }

    static function RetirarDeCuenta($tipoDeCuenta, $nroDeCuenta, $moneda, $importeARetirar)
    {
        $retorno = FALSE;
        $retiros = Retiro::leerDatosRetiroJson();
        $cuentas = Cuenta::LeerDatosBancoJson();
        $cuenta = Cuenta::ExisteCuentaPorNroYTipo($cuentas, $nroDeCuenta, $tipoDeCuenta);

        if(!is_string($cuenta))
        {
            $cuentaModificada = Cuenta::ModificarSaldoCuenta($importeARetirar, $nroDeCuenta, $tipoDeCuenta, $moneda, "-");

            if($cuentaModificada != NULL)
            {
                $idRetiro = Cuenta::GenerarIdAutoIncremental(100, "idRetiro.txt");
    
                $nuevoRetiro = new Retiro($cuentaModificada->_nombreYApellido, $cuentaModificada->_tipoDeDocumento, $cuentaModificada->_nroDocumento, $cuentaModificada->_mail, $cuentaModificada->_tipoDeCuenta, $cuentaModificada->_moneda, ($cuentaModificada->_saldoInicial) + $importeARetirar, $cuentaModificada->_nroDeCuenta, $cuentaModificada->_urlImagen, $importeARetirar, $idRetiro);
    
                $retiros[] = $nuevoRetiro;
                $retiroJson = json_encode($retiros, JSON_PRETTY_PRINT);
    
                file_put_contents("Retiro.json", $retiroJson);
    
                $retorno = TRUE;
            }
        }
        else
        {
            echo $cuenta;
        }


        return $retorno;
    }

    static function leerDatosRetiroJson()
    {
        $retiros = array();

        if(file_exists("Retiro.json"))
        {
            $rJson = file_get_contents("Retiro.json");  
            $arrayDeRetiros = json_decode($rJson, true);
    
            foreach($arrayDeRetiros as $value)
            {              
                $nuevoRetiro = new Retiro($value["_nombreYApellido"], $value["_tipoDeDocumento"], $value["_nroDocumento"], $value["_mail"], $value["_tipoDeCuenta"], $value["_moneda"], $value["_saldoInicial"], $value["_nroDeCuenta"], $value["_urlImagen"], $value["_monto"], $value["_idRetiro"]);
                $retiros[] = $nuevoRetiro;
            }
            
        }
        else
        {
            file_put_contents("Retiro.json", "[]");
        }

        return $retiros;
    }

    static function TotalDepositado($tipoDeCuenta, $moneda, $fecha = null)
    {
        $retiros = Retiro::leerDatosRetiroJson();
        $montoTotal = 0;

        if($fecha == null)
        {
            $fecha = new DateTime("");
            $fecha->sub(new DateInterval('P1D'));
            $fecha->format('Y-m-d');
        }

        foreach($retiros as $value)
        {
            if($value->_tipoDeCuenta == $tipoDeCuenta && $value->_moneda == $moneda && $value->_fecha == $fecha)
            {
                $montoTotal += $value->_monto;
            }
        }

        return $montoTotal;
    }

    static function BuscarRetirosDeUsuario($nroDeCuenta)
    {
        $retiros = Retiro::leerDatosRetiroJson();
        $retirosDelUsuario = NULL;

        foreach($retiros as $value)
        {
            if($value->_nroDeCuenta == $nroDeCuenta)
            {
                $retirosDelUsuario[] = $value;
            }
        }

        return $retirosDelUsuario;
    }



    static function BuscarRetirosEntreFechas($fechaInicio, $fechaFinal)
    {
        $retiros = Retiro::leerDatosRetiroJson();
        $retirosEntreFechas = NULL;
        $hayRetiro = false;

        foreach($retiros as $value)
        {
            if($value->_fecha >= $fechaInicio && $value->_fecha <= $fechaFinal)
            {
                $retirosEntreFechas[] = $value;
                $hayRetiro = true;
            }
        }

        if($hayRetiro)
        {
            usort($retirosEntreFechas, "Deposito::CompararNombre");
        }
        
        return $retirosEntreFechas;
    }

    static function CompararNombre($depositoUno, $depositoDos)
    {
        return strcmp($depositoUno->_nombreYApellido, $depositoDos->_nombreYApellido);
    }

    static function BuscarRetirosPorMoneda($moneda)
    {
        $retiros = Retiro::leerDatosRetiroJson();
        $retirosPorMoneda = NULL;

        foreach($retiros as $value)
        {
            if($value->_moneda == $moneda)
            {
                $retirosPorMoneda[] = $value;
            }
        }

        return $retirosPorMoneda;
    }

    static function BuscarRetirosPorTipoDeCuenta($tipoDeCuenta)
    {
        $retiros = Retiro::leerDatosRetiroJson();
        $retirosPorTipoDeCuenta = NULL;

        foreach($retiros as $value)
        {
            if($value->_tipoDeCuenta == $tipoDeCuenta)
            {
                $retirosPorTipoDeCuenta[] = $value;
            }
        }

        return $retirosPorTipoDeCuenta;
    }

    static function MostrarRetiros($retiros)
    {
        if($retiros != NULL)
        {
            foreach($retiros as $value)
            {
                echo "(Nombre y Apellido): " . $value->_nombreYApellido . " ";
                echo "(Tipo de Documento): " . $value->_tipoDeDocumento . " ";
                echo "(Nro de Documento): " . $value->_nroDocumento . " ";
                echo "(Mail): " . $value->_mail . " ";
                echo "(Tipo de Cuenta): " . $value->_tipoDeCuenta . " ";
                echo "(Moneda): " . $value->_moneda . " ";
                echo "(Saldo Inicial): " . $value->_saldoInicial . " ";
                echo "(Nro de Cuenta): " . $value->_nroDeCuenta . " ";
                echo "(URL Imagen): " . $value->_urlImagen . " ";
                echo "(Monto): " . $value->_monto . " ";
                echo "(Id Retiro): " . $value->_idRetiro . "</br></br>";
            }
        }
        else
        {
            echo "No hay retiros por mostrar </br>";
        }
    }

}



?>