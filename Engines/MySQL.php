<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:48
 */

namespace System\Engines;


class MySQL {

    const SELECT    = 1;
    const UPDATE    = 2;
    const DELETE    = 3;
    const WHERE     = 4;

    const FETCH_LAZY = 1;
    const FETCH_ASSOC    = 2;
    const FETCH_NUM    = 3;
    const FETCH_BOTH = 4;
    const FETCH_OBJ    = 5;

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

    public function select($Path){

        return new Select($this->Conn, $Path);

    }

    public function character($CharSet){

        //$this->Conn->query("SET CHARACTER SET {$CharSet}");

        $this->Conn->query("character_set_results = '{$CharSet}', character_set_client = '{$CharSet}', character_set_connection = '{$CharSet}', character_set_database = '{$CharSet}', character_set_server = '{$CharSet}'");

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

class SqlMaker{

    private $Path;
    private $QueryType;
    private $LastSqlSet = array();
    private $Keywords = array();
    private $LastQueryString;
    private $Blocks;
    private $Set = false;

    /**
     * @param $Path
     * @param $Type
     */
    public function __construct($Path, $Type){

        $this->LastQueryString = $Path;

        $this->Path = $Path;

        $this->QueryType = $Type;

        $this->Keywords = array(
            "&" => " AND ",
            "&&" => " AND ",
            "|" => " OR ",
            "||" => " OR ",
            "<<" => " < ",
        );

        $this->Blocks = array("{","(","[","]",")","}");

        $this->LastSqlSet["QueryString"] = null;

        $this->LastSqlSet["QueryValues"] = array();

        if(is_string($this->Path)){

            return $this->String();

        }

    }

    public function add($Path, $Type = null){

        if($Type!=null){ $this->QueryType = $Type; }

        $this->LastQueryString = ltrim($Path, "[/\#\$\:]");

        preg_match("/^;(&|&&|\||\|\|);(.*)$/i", ltrim($Path, "[/\#\$\:]"), $Split);

        $Mark = "/";

        if($this->Set){

            if(count($Split)>=1){

                $Mark = ";{$Split[1]};";

            }else{

                $Mark = ";&&;";

            }

        }

        $RunableString = preg_replace("/^;(&|&&|\||\|\|);/i", null, ltrim($Path, "[/\#\$\:]"));

        $this->Path .= "{$Mark}{$RunableString}";

        return $this->String();

    }

    private function StringS(){

        $Path = explode("/", ltrim($this->Path, "[/\#\$\:]"));

        if($this->QueryType==1){

            $From = explode(":", $Path[0]);

            $Columns = ($From[1]) ? $From[1] : "* ";

            $this->LastSqlSet["QueryString"] .= "SELECT ".$Columns;

            $this->LastSqlSet["QueryString"] .=  "FROM ".$From[0]." ";

            #$PregSplitPattern = "/;([&]|[\|]);/i";

            $String = preg_split("/;/i", $Path[1]);

            $Blocks = null;

            for($i=0;$i<count($String);$i++){

                if($i%2<=0){

                    $Blocks .= "( ";

                    $Wheres = str_replace(array("&&","||"), array(" AND ", " OR "), $String[$i]);

                    $Blocks .= ") ";

                }else{

                    $Blocks .= $String[$i]." ";

                }

            }

        }


    }

    private  function String($Path = null, $Set = true){

        $this->LastSqlSet["QueryString"] = null;

        $this->LastSqlSet["QueryValues"] = array();

        if($Path!=null){

           $this->Path = $Path;

        }

        $Path = explode("/", ltrim($this->Path, "[/\#\$\:]"));

        if(count($Path)>=2){

            $this->Set = true;

        }

        $Table = null;

        $Columns = null;

        $Blocks = null;

        $Values = array();

        if($this->QueryType==1){

            $Blocks .= "( ";

            $From = explode(":", $Path[0]);

            $Table = $From[0];

            $Columns = ($From[1]) ? $From[1]." " : "* ";

            $this->LastSqlSet["QueryString"] .= "SELECT ".$Columns;

            $this->LastSqlSet["QueryString"] .=  "FROM ".$From[0]." ";

            if((count($Path)==1||!isset($Path[1]))||(empty($Path[1])||strlen($Path[1])<=0)){

                return $this;

            }

            #$PregSplitPattern = "/;([&]|[\|]);/i";

            $String = preg_split("/;/i", $Path[1]);

            for($i=0;$i<count($String);$i++){

                if($i%2<=0){

                    $Blocks .= "( ";

                    $BlockSet = false;

                    if(in_array(substr($String[$i], 0, 1), $this->Blocks)){

                        $BlockSet = true;

                        $Blocks .= "( ";

                        $String[$i] = substr($String[$i], 1, (strlen($String[$i])-2));

                    }

                    $Ands = preg_split("/&&/i", $String[$i]);

                    $BlockAnd = null;

                    foreach($Ands as $And){

                        if(strpos($And, "||")){

                            $Ors = preg_split("/\|\|/i", $And);

                            $BlockOr = null;

                            foreach ($Ors as $Or) {

                                $Where = $this->ColAndVal($Or, "string");

                                $BlockOr .= $Where["string"]." OR ";

                                $Values = array_merge($Values, $Where["value"]);

                            }

                            $BlockAnd .= rtrim($BlockOr, " OR ")." ";

                        }else{

                            $Where = $this->ColAndVal($And, "string");

                            $BlockAnd .= $Where["string"]." AND ";

                            $Values = array_merge($Values, $Where["value"]);

                        }

                    }

                    $Blocks .= rtrim($BlockAnd, " AND ")." ";

                    $Blocks .= ") ";

                    /*
                     * Don´t remove this is for blockset probabilites
                     *
                     * if(in_array(substr($String[$i], -1, 1), $this->Blocks)){

                        $Blocks .= ") ";

                    }
                    */
                    if($BlockSet){

                        $Blocks .= ") ";

                    }

                }else{

                    $Blocks .= $this->Keywords[$String[$i]]." ";

                }

            }

            $Blocks .= ") ";

        }

        //$this->LastSqlSet["QueryString"] = "SELECT {$Columns} FROM `{$Table}` WHERE ".$Blocks;

        $WHERE = null;

        if(strpos($this->LastSqlSet["QueryString"], "WHERE")){

            $WHERE = "AND ".$Blocks;

        }else{

            $WHERE = "WHERE ".$Blocks;

        }

        $this->LastSqlSet["QueryString"] .= $WHERE;

        $this->LastSqlSet["QueryValues"] = $Values;

        return $this;

    }

    private function ColAndVal($Data, $Type){

        $Return = array();

        if($Type=="string"){

            preg_match("/:(.*):/i", $Data, $Split);

            $Parse = explode($Split[0], $Data);

            $Return["string"] = "`{$Parse[0]}` {$Split[1]} :".$Parse[0];

            $Return["value"] = array(":{$Parse[0]}" => $Parse[1]);

            return $Return;

        }

    }

    public function SqlSet(){

        return $this->LastSqlSet;

    }

    /*private function String(){

        $Path = explode("/", ltrim($this->Path, "[/\#\$\:]"));

        $Command = (strpos(strtolower($Path[0]), "select")) ? explode(":", $Path[0]) : array($Path[0]);

        $this->LastQuery .= strtoupper($Command[0])." ";

        $Columns = null;

        if(strtolower($Command[0])=="select"){

            $Columns = ($Command[1]) ? $Command[1] : "* ";

        }

        $this->LastQuery .= $Columns


    }*/

}

class Select{

    private $Conn;
    private $SqlMaker;
    private $Status;
    private $QueryString;
    private $QueryValues = array();
    private $LastSql = array();
    private $LastQuery;
    private $LastFetch;
    private $ErrorHandler;

    public function __construct(\PDO $Connection, $Path){

        $this->Conn = $Connection;

        $this->SqlMaker = new SqlMaker($Path, MySQL::SELECT);

        $this->QueryString = $this->SqlMaker->SqlSet()["QueryString"];

        $this->QueryValues = $this->SqlMaker->SqlSet()["QueryValues"];

        return $this;

    }

    public function where($Path){

        $SqlMaker = $this->SqlMaker->add($Path);

        $this->SqlMaker = $SqlMaker;

        $this->QueryString = $this->SqlMaker->SqlSet()["QueryString"];

        $this->QueryValues = $this->SqlMaker->SqlSet()["QueryValues"];

        return $this;

    }

    public function group($Path){

        $Path = "GROUP BY ".ltrim($Path, "[/\#\$\:]");

        $this->QueryString .= $Path." ";

        return $this;

    }

    public function order($Path){

        $Path = "ORDER BY ".ltrim($Path, "[/\#\$\:]");

        $this->QueryString .= $Path." ";

        return $this;

    }

    public function asc($Path){

        $Path = ltrim($Path, "[/\#\$\:]");

        if(preg_match_all("/(asc|ASC|desc|DESC)/i", $this->QueryString, $xxx)>=1){

            $this->QueryString = preg_replace("/(desc|DESC|asc|ASC)/i", "ASC", $this->QueryString);

            $this->QueryString = preg_replace("/(.*)ORDER BY (.*) ASC(.*)/", "$1ORDER BY ".$Path." ASC$3", $this->QueryString);

        }else{

            $this->QueryString .= "ORDER BY ".ltrim($Path, "[/\#\$\:]")." ASC ";

        }

        return $this;

    }

    public function desc($Path){

        $Path = ltrim($Path, "[/\#\$\:]");

        if(preg_match_all("/(asc|ASC|desc|DESC)/i", $this->QueryString, $xxx)>=1){

            $this->QueryString = preg_replace("/(desc|DESC|asc|ASC)/i", "DESC", $this->QueryString);

            $this->QueryString = preg_replace("/(.*)ORDER BY (.*) DESC(.*)/", "$1ORDER BY ".$Path." DESC$3", $this->QueryString);

        }else{

            $this->QueryString .= "ORDER BY ".ltrim($Path, "[/\#\$\:]")." DESC ";

        }

        return $this;

    }

    public function limit($Path){

        $Path = preg_split("/;|\//i", $Path);

        if(count($Path)>=3){

            return false;

        }

        $Path = "LIMIT ".implode(", ", $Path);;

        $this->QueryString .= $Path." ";

        return $this;

    }

    public function execute(Array $Settings = null, $ReturnFetch = true){

        if(!isset($Settings["fetch"])){

            $Settings["fetch"] = MySQL::FETCH_OBJ;

        }

        try{

            $this->LastQuery = $this->Conn->prepare($this->QueryString, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

            $this->LastQuery->execute($this->QueryValues);

            $Selected = $this->LastQuery;

            if(!$ReturnFetch){

                $this->LastFetch = $this->LastQuery->fetch($Settings["fetch"]);

                return $this->LastFetch;

            }

            return new Selected($this->Conn, $Selected->fetchAll(MySQL::FETCH_BOTH));

        }catch (\PDOException $Error){

            $this->ErrorHandler = new MySQLErrorHandler($Error);

            $this->Status = false;

            return $this;

        }

        return $this;

    }

    public function fetch($FETCH_TYPE = null){

        if($FETCH_TYPE==null){

            $FETCH_TYPE = MySQL::FETCH_OBJ;

        }

        $this->LastQuery->execute($this->QueryValues);

        $this->LastFetch = $this->LastQuery->fetch($FETCH_TYPE);

        return $this->LastFetch;

    }

    public function fetchAll($FETCH_TYPE = null){

        if($FETCH_TYPE==null){

            $FETCH_TYPE = MySQL::FETCH_OBJ;

        }

        $this->LastQuery->execute($this->QueryValues);

        $this->LastFetch = $this->LastQuery->fetchAll($FETCH_TYPE);

        return $this->LastFetch;

    }

    public function Status($Obj = null){

        return $this->Status;

    }

    public function LastFetch($Obj = null){

        return $this->LastFetch;

    }

    public function QueryString($Obj = null){

        return $this->QueryString;

    }

    public function ErrorHandler($Obj = null){

        return $this->ErrorHandler;

    }

}

class Selected{

    private $Conn;
    private $Source;
    private $Status = false;
    private $LastPrepared;
    private $LastQuery;
    private $LastSql = array();
    #private $SelectedRow;
    private $ErrorHandler;

    public function __construct(\PDO $Connection, Array $Source){

        $this->Conn = $Connection;

        $this->Source = $Source;

        return $this;

    }

    public function rowCount(){

        return count($this->Source);

    }

    public function row($Index){

        return $this->SelectedRow = new Row($this->Conn, $this->Source[$Index]);

    }

}

class Row{

    private $Conn;
    private $Source;
    private $Status = false;
    private $LastPrepared;
    private $LastQuery;
    private $LastSql = array();
    private $SelectedCol;
    private $ErrorHandler;

    public function __construct(\PDO $Connection, Array $Source){

        $this->Conn = $Connection;

        $this->Source = $Source;

        return $this;

    }

    public function col($Names){

        $Path = preg_split("/;|\//i", $Names);

        $Cols = array();

        foreach ($this->Source as $Col => $Value) {

            if(in_array($Col, $Path)){

                if(is_numeric($Col)){

                    continue;

                }

                $Cols = array_merge($Cols, array($Col => $Value));

            }

        }

        return new Column($this->Conn, $Cols);

    }

    public function dump(){

        var_dump($this->Source);

    }

}

class Column{

    private $Conn;
    private $Source;
    private $Status = false;
    private $LastPrepared;
    private $LastQuery;
    private $QueryString;
    private $QueryValues;
    private $LastSql = array();
    private $SqlMaker;
    private $ErrorHandler;

    public function __construct(\PDO $Connection, Array $Source){

        $this->Conn = $Connection;

        $this->Source = $Source;

        return $this;

    }

    public function select($Path){

        $Path = explode("/", ltrim($Path, "[/\#\$\:]"));

        if(count($Path)>1){

            foreach ($this->Source as $Col => $Val) {

                //$Path[1] = preg_replace("/(.*):(.*):".$Col."(.*)/i", "$0 :: $1:$2:".$Val."$3", $Path[1]);

                $Path = $Path[0]."/".str_replace(":".$Col, ":".$Val, $Path[1]);

            }

        }

        var_dump($Path);

        return new Select($this->Conn, $Path);

        /*$this->SqlMaker = new SqlMaker($Path, MySQL::SELECT);

        $this->QueryString = $this->SqlMaker->SqlSet()["QueryString"];

        $this->QueryValues = $this->SqlMaker->SqlSet()["QueryValues"];

        return $this;*/

    }

    public function dump(){

        var_dump($this->Source);

    }

}

class Table{

    private $Conn;
    private $Status = false;
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

        $this->LastQuery = $this->Conn->prepare($Select.$Where, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

        $this->LastPrepared = $this->LastQuery;

        try{

            $this->LastQuery->execute($Values);

            if( $this->LastQuery->rowCount()>=1){

                $this->Status = true;

                return true;

            }else{

                $this->Status = false;

                return false;

            }

        }catch (\PDOException $Error){

            $this->ErrorHandler = new MySQLErrorHandler($Error);

            $this->Status = false;

            return false;

        }

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