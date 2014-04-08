<?php
class DATABASE_CONFIG {
    public $default;

    public $development = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => '192.168.1.224',
        'login' => 'root',
        // 'password' => '',
        'database' => 'badoo',
        'encoding' => 'utf8'
    );

    public $production = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => '127.0.0.1',
        'login' => 'root',
        //'password' => 'password',
        'database' => 'badoo',
        'prefix' => '',
        'encoding' => 'utf8',
    );
/*
	public $mysql = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => 'cakephp',
		'encoding' => 'utf8'
	);

	public $postgres = array(
		'datasource' => 'Database/Postgres',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 5432,
		'login' => 'postgres',
		'password' => 'postgres',
		'database' => 'cakephp',
        'schema' => 'public',
		'encoding' => 'utf8'
	);
*/
    public function __construct(){
        // default configuration for testing
        $this->default = $this->development;

        // switch environment settings for production
        if(Configure::read('env') == 'production'){
            $this->default = $this->production;
        }
    }
}
