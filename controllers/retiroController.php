<?php

require_once './models/retiro.php';
require_once './models/cuenta.php';

Class retiroController extends retiro
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $nroDeCuenta = $params['nroDeCuenta'];
        $tipoDeCuenta = $params['tipoDeCuenta'];
        $moneda = $params['moneda'];
        $monto = $params['monto'];

        $dato = Cuenta::ExisteCuentaPorNroYTipo($nroDeCuenta, $tipoDeCuenta);

        if(!is_string($dato))
        {
            if($dato->moneda == $moneda)
            {
                $saldoAnterior = Cuenta::ObtenerSaldoCuenta($nroDeCuenta);

                if($monto <= $saldoAnterior)
                {
                    $nuevoRetiro = new Retiro();
                    $nuevoRetiro->nroDeCuenta = $nroDeCuenta;
                    $nuevoRetiro->moneda = $moneda;
                    $nuevoRetiro->monto = $monto;
                    $nuevoRetiro->fecha = date('Y-m-d');  
                     
                    Cuenta::ModificarSaldoCuenta($nroDeCuenta, $tipoDeCuenta . $moneda, $saldoAnterior - $monto);
        
                    $nuevoRetiro->CrearRetiro();
        
                    $payload = json_encode(array("mensaje" => "El retiro se hizo correctamente"));
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No hay suficiente dinero en la cuenta para retirar ese monto"));
                }
            }
            else
            {
                $payload = json_encode(array("mensaje" => "La cuenta no utiliza esa moneda"));
            }

        }
        else
        {
            $payload = json_encode(array("mensaje" => "{$dato}"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');

    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Retiro::ObtenerTodos();

        if(!empty($lista))
        {
            $payload = json_encode(array("ListaDeRetiros" => $lista));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay retiros por mostrar")); 
        }  

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MovimientoA($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $tipoDeCuenta = $params["tipoDeCuenta"];
        $moneda = $params["moneda"];
        $montoTotal = 0;
        if(isset($params["fecha"]))
        {
            $fecha = $params["fecha"];
        }
        else
        {
            $fecha = new DateTime("");
            $fecha->sub(new DateInterval('P1D'));
            $fecha->format('Y-m-d');
        }

        $montoTotal = Retiro::TotalRetirado($tipoDeCuenta, $moneda, $fecha);

        $payload = json_encode(array("mensaje" => "El total retirado es {$montoTotal}"));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
        
    }

    public function MovimientoB($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $nroDeCuenta = $params["nroDeCuenta"];
        $retiros = Retiro::ObtenerRetirosDeCuenta($nroDeCuenta);

        if($retiros != NULL)
        {
            $payload = json_encode(array("mensaje" => $retiros));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay retiros por mostrar"));
        }        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');       
    }

    public function MovimientoC($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $fechaDeInicio = $params["fechaDeInicio"];
        $fechaFinal = $params["fechaFinal"];

        $retiros = Retiro::ObtenerRetirosEntreFechas($fechaDeInicio, $fechaFinal);

        if($retiros != NULL)
        {
            $payload = json_encode(array("mensaje" => $retiros));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay retiros por mostrar"));
        }
        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');     
    }

    public function MovimientoD($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $tipoDeCuenta = $params["tipoDeCuenta"];

        $retiros = Retiro::ObtenerRetirosPorTipoDeCuenta($tipoDeCuenta);

        if($retiros != NULL)
        {
            $payload = json_encode(array("mensaje" => $retiros));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay retiros por mostrar"));
        }
        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');     
    }
    public function MovimientoE($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $moneda = $params["moneda"];

        $retiros = Retiro::ObtenerRetirosPorMoneda($moneda);

        if($retiros != NULL)
        {
            $payload = json_encode(array("mensaje" => $retiros));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay retiros por mostrar"));
        }
        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');     
    }
} 

?>