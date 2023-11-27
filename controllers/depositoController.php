<?php

require_once './models/deposito.php';
require_once './models/cuenta.php';

Class depositoController extends deposito
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
            $cuenta = $dato;

            if($cuenta->moneda == $moneda)
            {
                $nuevoDeposito = new Deposito();
                $nuevoDeposito->nroDeCuenta = $nroDeCuenta;
                $nuevoDeposito->urlImagen = Deposito::GuardarImagenDeposito('ImagenesDeDepositos2023/', $_FILES['foto'], $cuenta->tipoDeCuenta, $cuenta->nroDeCuenta);
                $nuevoDeposito->moneda = $moneda;
                $nuevoDeposito->monto = $monto;
                $nuevoDeposito->fecha = date('Y-m-d');  
                $saldoAnterior = Cuenta::ObtenerSaldoCuenta($nroDeCuenta); 
                Cuenta::ModificarSaldoCuenta($nroDeCuenta, $tipoDeCuenta . $moneda, $saldoAnterior + $monto);
    
                $nuevoDeposito->CrearDeposito();
    
                $payload = json_encode(array("mensaje" => "El deposito se hizo correctamente"));
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
        $lista = Deposito::ObtenerTodos();
        if(!empty($lista))
        {
            $payload = json_encode(array("ListaDeDepositos" => $lista));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay depositos por mostrar")); 
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

        $montoTotal = Deposito::TotalDepositado($tipoDeCuenta, $moneda, $fecha);

        $payload = json_encode(array("mensaje" => "El total depositado es {$montoTotal}"));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
        
    }

    public function MovimientoB($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $nroDeCuenta = $params["nroDeCuenta"];
        $depositos = Deposito::ObtenerDepositosDeCuenta($nroDeCuenta);

        if($depositos != NULL)
        {
            $payload = json_encode(array("mensaje" => $depositos));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay depositos por mostrar"));
        }        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');       
    }

    public function MovimientoC($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $fechaDeInicio = $params["fechaDeInicio"];
        $fechaFinal = $params["fechaFinal"];

        $depositos = Deposito::ObtenerDepositosEntreFechas($fechaDeInicio, $fechaFinal);

        if($depositos != NULL)
        {
            $payload = json_encode(array("mensaje" => $depositos));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay depositos por mostrar"));
        }
        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');     
    }

    public function MovimientoD($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $tipoDeCuenta = $params["tipoDeCuenta"];

        $depositos = Deposito::ObtenerDepositosPorTipoDeCuenta($tipoDeCuenta);

        if($depositos != NULL)
        {
            $payload = json_encode(array("mensaje" => $depositos));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay depositos por mostrar"));
        }
        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');     
    }
    public function MovimientoE($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $moneda = $params["moneda"];

        $depositos = Deposito::ObtenerDepositosPorMoneda($moneda);

        if($depositos != NULL)
        {
            $payload = json_encode(array("mensaje" => $depositos));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay depositos por mostrar"));
        }
        

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');     
    }
} 

?>