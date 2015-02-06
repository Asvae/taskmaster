<?php
namespace ptejada\uFlex;

/**
 * Extended User object. Configures db access.
 * 
 *
 * @package ptejada\uFlex
 * @author  Asva <ontrew@gmail.com>
 */
class MyUser extends User {
    
    public function __construct()
    {
        parent::__construct();

        //Add database credentials
        $this->config->database->host = 'localhost';
        $this->config->database->user = 'root';
        $this->config->database->password = '';
        $this->config->database->name = 'asva_db';

        // Start object construction
        $this->start();
    }
}