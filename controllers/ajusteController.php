<?php

require_once './models/ajuste.php';

class ajusteController extends Ajuste
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $tipoDeAjuste = $params['tipoDeAjuste'];
        $motivo = $params['motivo'];
        $idRetiro = $params['idRetiro'];
        $idDeposito = $params['idDeposito'];
        $encontroIdRetiro = false;
        $encontroIdDeposito = false;

        if($tipoDeAjuste == "Extraccion")
        {
            if(!Ajuste::VerificarSiElAjusteExiste($tipoDeAjuste, $idRetiro))
            {
                $retiros = Retiro::ObtenerTodos();

                if(!empty($retiros))
                {
                    foreach($retiros as $value)
                    {
                        if($value['idRetiro'] == $idRetiro)
                        {
                            $cuenta = Cuenta::ObtenerCuenta($value['nroDeCuenta']);
                            $encontroIdRetiro = true;
                            if($cuenta)
                            {
                                $saldoAnterior = Cuenta::ObtenerSaldoCuenta($cuenta->nroDeCuenta);
                                Cuenta::ModificarSaldoCuenta($cuenta->nroDeCuenta, $cuenta->tipoDeCuenta, $saldoAnterior + $value['monto']);
                                $nuevoAjuste = new Ajuste();
                                $nuevoAjuste->motivo = $motivo;
                                $nuevoAjuste->monto = $value['monto'];
                                $nuevoAjuste->idRetiro = $idRetiro;
                                $nuevoAjuste->CrearAjuste();
                                $payload = json_encode(array("mensaje" => "El ajuste se hizo correctamente"));
                                
                                break; 
                            }
                            else
                            {
                                $payload = json_encode(array("mensaje" => "No existe una cuenta con ese nroDeCuenta"));
                                break;  
                            }   
                        }
                    }
                    if(!$encontroIdRetiro)
                    {
                        $payload = json_encode(array("mensaje" => "No hay retiro con ese id"));
                    }
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No se encontraron retiros"));
                }
            }
            else
            {
                $payload = json_encode(array("mensaje" => "El ajuste ya se realizo anteriormente"));
            }
        }
        else if($tipoDeAjuste == "Deposito")
        {
            if(!Ajuste::VerificarSiElAjusteExiste($tipoDeAjuste, $idDeposito))
            {
                $depositos = Deposito::ObtenerTodos();

                if(!empty($depositos))
                {
                    foreach($depositos as $value)
                    {
                        if($value['idDeposito'] == $idDeposito)
                        {
                            $cuenta = Cuenta::ObtenerCuenta($value['nroDeCuenta']);
                            $encontroIdDeposito = true;

                            if($cuenta)
                            {
                                $saldoAnterior = Cuenta::ObtenerSaldoCuenta($cuenta->nroDeCuenta);

                                if($value['monto'] <= $saldoAnterior)
                                {
                                    Cuenta::ModificarSaldoCuenta($cuenta->nroDeCuenta, $cuenta->tipoDeCuenta, $saldoAnterior - $value['monto']);
                                    $nuevoAjuste = new Ajuste();
                                    $nuevoAjuste->motivo = $motivo;
                                    $nuevoAjuste->monto = $value['monto'];
                                    $nuevoAjuste->idDeposito = $idDeposito;
                                    $nuevoAjuste->CrearAjuste();
                                    $payload = json_encode(array("mensaje" => "El ajuste se hizo correctamente"));
                                    break; 
                                }
                                else
                                {
                                    $payload = json_encode(array("mensaje" => "El monto de la extraccion es superior al saldo de la cuenta"));
                                    break; 
                                }
                            }
                            else
                            {
                                $payload = json_encode(array("mensaje" => "No existe una cuenta con ese nroDeCuenta"));
                                break;  
                            }

                        }
                    }
                    if(!$encontroIdDeposito)
                    {
                        $payload = json_encode(array("mensaje" => "No hay deposito con ese id"));
                    }
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No se encontraron depositos"));
                }
            }
            else
            {
                $payload = json_encode(array("mensaje" => "El ajuste ya se realizo anteriormente"));
            }
        }



        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');

    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Ajuste::ObtenerTodos();
        if(!empty($lista))
        {
            $payload = json_encode(array("ListaDeAjustes" => $lista));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay ajustes por mostrar")); 
        }        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>