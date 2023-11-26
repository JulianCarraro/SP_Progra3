<?php

class AccesoDatos
{
    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPass = '';
    private $dbName = 'banco'; 
    private $objetoPDO;
    private static $objAccesoDatos;

    private function __construct()
    {
       try
       {
            $this->objetoPDO = new PDO("mysql:host=$this->dbHost; dbname=$this->dbName", $this->dbUser, $this->dbPass);
            $this->objetoPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
       } 
       catch(PDOException $e)
       {
            print "Error: " . $e->getMessage();
            exit();
       }
    }

    
    public static function ObtenerInstancia()
    {
        if(!isset(self::$objAccesoDatos))
        {
            self::$objAccesoDatos = new AccesoDatos();
        }

        return self::$objAccesoDatos;
    }

    public function PrepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    
}

?>