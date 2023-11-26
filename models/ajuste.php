<?php

include_once("deposito.php");
include_once("retiro.php");
class Ajuste
{
    public $_nroDeCuenta;
    public $_motivo;
    public $_monto;
    public $_idDeposito;
    public $_idRetiro;

    public function __construct($nroDeCuenta, $motivo, $monto, $idDeposito, $idRetiro)
    {
        $this->_nroDeCuenta = $nroDeCuenta;
        $this->_motivo = $motivo;
        $this->_monto = $monto;
        $this->_idDeposito = $idDeposito;
        $this->_idRetiro = $idRetiro;
    }

    static function AjusteCuenta($tipoDeAjuste, $motivo, $idDeposito, $idRetiro)
    {
        $retorno = false;
        $ajustes = Ajuste::leerDatosAjusteJson();

        if($tipoDeAjuste == "Extraccion")
        {
            if(!Ajuste::VerificarSiElAjusteExiste($tipoDeAjuste, $idRetiro))
            {
                $retiros = Retiro::leerDatosRetiroJson();

                if($retiros != null)
                {
                    foreach($retiros as $value)
                    {
                        if($value->_idRetiro == $idRetiro)
                        {              
                            $cuentaModificada = Cuenta::ModificarSaldoCuenta($value->_monto, $value->_nroDeCuenta, $value->_tipoDeCuenta, $value->_moneda, "+");
                            $nuevoAjuste = new Ajuste($cuentaModificada->_nroDeCuenta, $motivo, $value->_monto, null, $value->_idRetiro);
                            $ajustes[] = $nuevoAjuste;
                            $retorno = true;
                            break; 
                        }
                    }
                }
                else
                {
                    echo "No hay retiros en la cuenta </br>";
                }

            }
            else
            {
                echo "Este Ajuste ya se hizo </br>";
            }
        }
        else if($tipoDeAjuste == "Deposito")
        {
            if(!Ajuste::VerificarSiElAjusteExiste($tipoDeAjuste, $idDeposito))
            {
                $depositos = Deposito::leerDatosDepositoJson();

                if($depositos != null)
                {
                    foreach($depositos as $value)
                    {
                        if($value->_idDeposito == $idDeposito)
                        {
                            $cuentaModificada = Cuenta::ModificarSaldoCuenta($value->_monto, $value->_nroDeCuenta, $value->_tipoDeCuenta, $value->_moneda, "-");
                            $nuevoAjuste = new Ajuste($cuentaModificada->_nroDeCuenta, $motivo, $value->_monto, $value->_idDeposito, null);
                            $ajustes[] = $nuevoAjuste;
                            $retorno = true;
                            break;
                        }
                    }
                }
                else
                {
                    echo "No hay depositos en la cuenta </br>";
                }
            }
            else
            {
                echo "Este Ajuste ya se hizo </br>";
            }
        }

        $archivo = fopen("Ajuste.json", "w");
        $cuentasEnJson = json_encode($ajustes, JSON_PRETTY_PRINT);

        fwrite($archivo, $cuentasEnJson);

        fclose($archivo);

        return $retorno;
    }

    static function VerificarSiElAjusteExiste($tipoDeAjuste, $id)
    {
        $ajustes = Ajuste::leerDatosAjusteJson();
        $retorno = 0;

        if($ajustes != null)
        {
            foreach($ajustes as $value)
            {
                if($tipoDeAjuste == "Extraccion")
                {
                    if($value->_idRetiro == $id)
                    {
                        $retorno = true;
                    }
                }
                else if($tipoDeAjuste == "Deposito")
                {
                    if($value->_idDeposito == $id)
                    {
                        $retorno = true;
                    }
                }
            }
        }

        return $retorno;
    }

    static function leerDatosAjusteJson()
    {
        $ajustes = array();

        if(file_exists("Ajuste.json"))
        {
            $aJson = file_get_contents("Ajuste.json");  
            $arrayDeAjustes = json_decode($aJson, true);
    
            foreach($arrayDeAjustes as $value)
            {              
                $nuevoAjuste = new Ajuste($value["_nroDeCuenta"], $value["_motivo"], $value["_monto"], $value["_idDeposito"], $value["_idRetiro"]);
                $ajustes[] = $nuevoAjuste;
            }
            
        }
        else
        {
            file_put_contents("Ajuste.json", "[]");
        }

        return $ajustes;
    }

}

?>