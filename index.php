<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/cuentaController.php';
require_once './controllers/retiroController.php';
require_once './controllers/depositoController.php';
require_once './controllers/ajusteController.php';


// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//Routes
// $app->get('[/]', function (Request $request, Response $response) {    
//     $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('[/]', function (Request $request, Response $response) {    
//     $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/test', function (Request $request, Response $response) {    
//     $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });



$app->group('/cuentas', function (RouteCollectorProxy $group)
{
    $group->get('/MovimientoF', \cuentaController::class . ':MovimientoF');
    $group->get('/TraerCuenta', \cuentaController::class . ':TraerUno');
    $group->get('[/]', \cuentaController::class . ':TraerTodos');
    $group->post('/AltaCuenta', \cuentaController::class . ':CargarUno');
    $group->post('/ConsultarCuenta', \cuentaController::class . ':ConsultarCuentas');
    $group->put('[/]', \cuentaController::class . ':ModificarUno');
    $group->delete('[/]', \cuentaController::class . ':BorrarUno');
})->add(new AuthMW());

$app->group('/depositos', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \depositoController::class . ':TraerTodos')->add(new AuthCajeroMW());
    $group->get('/MovimientoA', \depositoController::class . ':MovimientoA')->add(new AuthSupervisorMW());
    $group->get('/MovimientoB', \depositoController::class . ':MovimientoB')->add(new AuthSupervisorMW());
    $group->get('/MovimientoC', \depositoController::class . ':MovimientoC')->add(new AuthSupervisorMW());
    $group->get('/MovimientoD', \depositoController::class . ':MovimientoD')->add(new AuthSupervisorMW());
    $group->get('/MovimientoE', \depositoController::class . ':MovimientoE')->add(new AuthSupervisorMW());
    $group->post('[/]', \depositoController::class . ':CargarUno')->add(new AuthCajeroMW());
})->add(new AuthMW())->add(new AuthCajeroMW());

$app->group('/retiros', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \retiroController::class . ':TraerTodos')->add(new AuthCajeroMW());
    $group->get('/MovimientoA', \retiroController::class . ':MovimientoA')->add(new AuthSupervisorMW());
    $group->get('/MovimientoB', \retiroController::class . ':MovimientoB')->add(new AuthSupervisorMW());
    $group->get('/MovimientoC', \retiroController::class . ':MovimientoC')->add(new AuthSupervisorMW());
    $group->get('/MovimientoD', \retiroController::class . ':MovimientoD')->add(new AuthSupervisorMW());
    $group->get('/MovimientoE', \retiroController::class . ':MovimientoE')->add(new AuthSupervisorMW());
    $group->post('[/]', \retiroController::class . ':CargarUno')->add(new AuthCajeroMW());
})->add(new AuthMW());

$app->group('/ajustes', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \ajusteController::class . ':TraerTodos');
    $group->post('[/]', \ajusteController::class . ':CargarUno');
})->add(new AuthMW())->add(new AuthSupervisorMW());

$app->group('/cuentas', function (RouteCollectorProxy $group)
{
    $group->post('[/]', \cuentaController::class . ':CargarUno');
});

$app->run();
