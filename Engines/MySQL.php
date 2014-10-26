<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:48
 */

namespace System\Engines;


class MySQL {

    private $Conn;
    private $Status = true;
    private $ErrorHandler;
    /*
    private $Err;
    private $Error;
    private $ErrNo;
    private $ErrInfo;
    private $ErrorMessage;
    */

    public function __construct(){

        return $this;

    }

    public function connect(Array $Config){

        $mysql_server = (@$Config["host"]) ? $Config["host"] : $Config["server"];
        $mysql_database = (@$Config["db"]) ? $Config["db"] : $Config["database"];
        $mysql_user = (@$Config["user"]) ? $Config["user"] : $Config["username"];
        $mysql_password = (@$Config["pass"]) ? $Config["pass"] : $Config["password"];

        try{

            $this->Conn = new \PDO("mysql:host={$mysql_server};dbname={$mysql_database}", $mysql_user, $mysql_password);

        }catch (\PDOException $Error){

            $this->Status = false;

            $this->ErrorHandler = new MySQLErrorHandler($Error);

            //$this->ErrorHandler($Error);

        }

        return $this;

    }

    public function table($Path){

        return new Table($this->Conn, ltrim($Path, "[/\#\$\:]"));

    }

    public function character($CharSet){

        $this->Conn->query("SET CHARACTER SET {$CharSet}");

    }

    /*private function ErrorHandler(\PDOException $Error){

        $this->Status = false;

        $this->Err = $Error;

        $this->ErrNo = $Error->getCode();

        $this->Error = $Error->getMessage();

        $this->ErrInfo = $Error->errorInfo;

        $this->ErrorMessage = "{$Error->getCode()} : {$Error->getMessage()} in {$Error->getFile()} on line: {$Error->getLine()}";

    }*/

    public function Status($Obj = null){

        return $this->Status;

    }

    public function ErrorHandler($Obj = null){

        return $this->ErrorHandler;

    }

}

class Table{

    private $Conn;
    private $Status = true;
    private $Table;
    private $LastPrepared;
    private $LastQuery;
    private $LastSql = array();
    private $ErrorHandler;

    public function __construct(\PDO $Connection, $Table){

        $this->Conn = $Connection;

        $this->Table = $Table;

    }

    public function in(Array $Path){

        $Select = "SELECT * FROM {$this->Table} WHERE ";

        $Where = null;

        $Values = array();

        foreach ($Path as $Column => $Value){

            $VAR = ltrim($Column, "[/\#\$\:]");

            $Where .= "`".$VAR."` = :".$VAR." AND ";

            $Values[":".$VAR] = $Value;

        }

        $Where = rtrim($Where, " AND ");

        $this->LastSql = array(
            "sql" => $Select.$Where,
            "values" => $Values
        );

        $this->LastPrepared = $this->Conn->prepare($Select.$Where, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

        var_dump($this->LastSql);

        var_dump($this->LastPrepared->execute($Values)->fetchAll());

        try{

            $this->LastQuery = $this->LastPrepared->execute($Values);

            $this->Status = true;

        }catch (\PDOException $Error){

            $this->ErrorHandler = new MySQLErrorHandler($Error);

            $this->Status = false;

        }

        //exit($Select.$Where);

        return $this;

    }

    public function LastQuery(){

        return $this->LastQuery;

    }

    public function LastSql(){

        return $this->LastSql;

    }

    public function LastPrepared(){

        return $this->LastPrepared;

    }

    public function Status($Obj = null){

        return $this->Status;

    }

    public function ErrorHandler($Obj = null){

        return $this->ErrorHandler;

    }

}

class MySQLErrorHandler{

    private $Err;
    private $Error;
    private $ErrNo;
    private $ErrInfo;
    private $ErrorMessage;

    private function __construct(\PDOException $Error){

        $this->Err = $Error;

        $this->ErrNo = $Error->getCode();

        $this->Error = $Error->getMessage();

        $this->ErrInfo = $Error->errorInfo;

        $this->ErrorMessage = "{$Error->getCode()} : {$Error->getMessage()} in {$Error->getFile()} on line: {$Error->getLine()}";

    }

    public function Err($Obj = null){

        return $this->Err;

    }

    public function Error($Obj = null){

        return $this->Error;

    }

    public function ErrNo($Obj = null){

        return $this->ErrNo;

    }

    public function ErrorInfo($Obj = null){

        return $this->ErrInfo;

    }

    public function ErrorMessage($Obj = null){

        return $this->ErrorMessage;

    }

}