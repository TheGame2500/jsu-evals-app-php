<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('./kint-master/Kint.class.php');
class evals{
    var $form_id;
    var $columnsArray=[];
    var $db_connection;
    
    public function __construct($form_id){
        require_once('../config/db.php');
        $this->blackListColumns = [39,44,49,53,57,30,27,64,40]; //weird label columns we don't actually need
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); 
        $this->db_connection->set_charset('utf8');
        $this->form_id = $form_id; 
        $this->generateEvalTable();
    } 
    
    private function generateEvalTable(){
        $this->createTable();
        $this->populateTable();
    }

    private function createTable(){
        $this->getColumns();
        $this->createTableFromColumns();
    }

    private function getColumns(){
        $sql = 'SELECT * FROM d5a_ninja_forms_fields WHERE form_id ='.$this->form_id;
        $sql = $this -> db_connection -> query($sql);
        
        while($row = $sql->fetch_object()){
           $unserializedData = unserialize($row->data);
           $fieldID = $row->id;
           $column['label'] = str_replace(' ','_',$unserializedData['label']);
           $column['label'] = str_replace("\t",'',$column['label']);
           if($this->checkDuplicateColumn($column['label'])) $column['label'].='_parinte';
           //creating the columns in db with decent name lengths
           $column['name'] = substr($column['label'],0,20);
           $column['id'] = $fieldID;
           if($column['label'] !== "") 
               array_push($this->columnsArray,$column);
        }

        //remove blacklisted columns
        forEach($this->columnsArray as $key => $column){
            if(in_array($column['id'],$this->blackListColumns)){
                unset($this->columnsArray[$key]);
            }
        }
    }

    private function createTableFromColumns(){
        $sqlBeginning = 
            "CREATE TABLE `login`.`evals` (
              `id` INT NOT NULL AUTO_INCREMENT,\r\n";
              

        forEach($this -> columnsArray as $column){
            $sqlBeginning.='`'.$column['name']."` LONGTEXT,\r\n";
        }


        $sqlEnding = "`progress` INT NOT NULL ,  PRIMARY KEY (`id`))";
        $sql = $sqlBeginning.$sqlEnding;
        $this->db_connection->query($sql);
    }

    //beacuse yes, ninja forms is so stupid it has two fields completely the same with no distinguishable characteristics
    private function checkDuplicateColumn($columnLabel){
        $same = false;
        forEach($this ->columnsArray as $column){
            if(strCmp($column['label'],$columnLabel) == 0) $same = true;
        }
        return $same;
    }

    private function populateTable(){
        $allPosts = $this -> getAllPosts() -> fetch_all();
        forEach($allPosts as $key1 => $post){
            $row = [$post];
            forEach($allPosts as $key2 => $otherPost){
                if($post[1]==$otherPost[1] && $post[2]!==$otherPost[2]){
                    array_push($row,$otherPost);
                    unset($allPosts[$key2]);
                }
            }
            unset($allPosts[$key1]);
            if(count($row) !== 1)
                d($row);
        }
        die();
    }

    private function getAllPosts(){
        $formID = $this -> form_id; 
        $columns = "";
        
        forEach($this -> columnsArray as $column){
            $columns.="'_field_".$column['id']."',";
        }
        $columns = rtrim($columns, ',');

        $sql = "select * from d5a_postmeta where meta_key in($columns) and post_id in(select post_id from
                d5a_postmeta where meta_key = '_form_id' and meta_value=$formID)";
        return $this -> db_connection -> query($sql);
    }
}

$test = new evals(6);
?>
</body>
