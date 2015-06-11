<?php


/**
 * To build a single class
 */
Class DB {
    private static $_instance;

    private $pdo;
    private $conf;

    private $table;

    private function __construct() {
        $this->loadConf()->connect(); 
        $this->table = 'table';
    }

    public function __clone() {
        return false;
    }

    private function loadConf() {
        $this->conf = new \Yaf\Config\Ini( APP_PATH . '/conf/db.ini', 'product' );
        return $this;
    }

                    
    private function connect() {
        try {
            $this->pdo = new PDO( 
                sprintf( 'mysql:host=%s;dbname=%s', $this->conf->mysql->host, $this->conf->mysql->dbname ),
                $this->conf->mysql->username, 
                $this->conf->mysql->password,
                array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                )
            ); 

            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            echo 'xxxx';

            var_dump( $this->pdo->inTransaction() );

        } catch( PDOException $e ) {
            die( $e->getMessage() );
        }

        return $this;
    }

    static public function getInstance() {
        if( !self::$_instance instanceof self ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
