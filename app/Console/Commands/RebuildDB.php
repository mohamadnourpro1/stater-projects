<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use mysqli;

class RebuildDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rebuild_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild Database for project';
      /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
        public function handle()
    {
        $host = env('DB_HOST');
        $password = env('DB_PASSWORD');
        $username = env('DB_USERNAME');
        $database = env('DB_DATABASE');
        $conn = new mysqli($host, $username, $password);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $sql = "DROP DATABASE IF EXISTS `$database`";
        if ($conn->query($sql) === TRUE) {
            echo "\e[32mSucessfully dropped database $database!\n";
        } else {
            echo "\e[91mError dropping database: " . $conn->error;
            return 1;
        }
        $sql = "CREATE DATABASE `$database`";
        if ($conn->query($sql) === TRUE) {
            echo "\e[32mSucessfully created database $database!\n";
        } else {
            echo "\e[91mError creating database: " . $conn->error;
            return 1;
        }
        $query = '';
        $sqlScript = file(storage_path() . '\db.sql');
        $conn->select_db($database);
        foreach ($sqlScript as $line)	{
            
            $startWith = substr(trim($line), 0 ,2);
            $endWith = substr(trim($line), -1 ,1);
            
            if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                continue;
            }
                
            $query = $query . $line;
            if ($endWith == ';') {
                if ($conn->query($query) === FALSE) {
                    echo "\e[91mError importing database: " . $conn->error;
                    return 1;
                }
                $query= '';		
            }
        }
        $conn->close();
        echo "\e[32mSucessfully completed database $database!\n\e[39m";
        return 0;
    }
}
