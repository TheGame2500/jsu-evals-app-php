<?php
//this acts as an API to the fucked up Ninja Forms bullshit DB..
//initialise by giving it the form id
//if you don't know the form id you need, you probably should not use this (you can usually find out from the UI of the WP admin panel
//
//It can generate a table with all the data submitted in the specific form, with decent column names (not that readable though)
//It can also sort through the bullshit duplicated submissions or submissions with duplicated fields...
//
//Death to Ninja Forms, Death to Word Press.. Long live independent programmers not needing that fucking BS WP
//
//Like seriously why would you build something that isolated... It's like the Smaug of internet.. No one can communicate with it efficiently to 
//build some custom code.. And that is why this bullshit, ugly, fat, shameless class is in existence..
//
//If you have to use this.. ever.. please start NOT using WP...
//
//Build in Anno Domini 2016.. Some fucking year, eh? I honestly hope no one ever reads this..

//Btw if you need to create another table instead of 'evals' just change 'login.evals' into whatever is appropiate for you..
//
//Use this and then happy coding whatever app you're coding!

ini_set('display_errors', 1);
error_reporting(E_ALL);
class EvalsAPI{
    var $form_id;
    var $columnsArray=[];
    var $db_connection;
    
    public function __construct($form_id){
        require_once('../config/db.php');
        $this->blackListColumns = [39,44,49,53,57,30,27,64,40]; //weird label columns we don't actually need, you should check them out by looking into the ninja_form_fields or something like that and see what columns are not actually columns, but labels, descriptions and whatever the fuck the Ninja Forms developers thought it would be smart to put into their DB.. And they didn't even write an API to interact with it outsite of Wordpress..
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); 
        $this->db_connection->set_charset('utf8');
        $this->form_id = $form_id; 
    } 

    public function getPrettyColumns(){
        $this -> getColumns();
        return $this -> columnsArray;
    }
    
    public function generateEvalTable(){
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
           $column['prettyName'] = str_replace('_',' ', $column['label']); //God forbid this line of code, I'm on a deadline..
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
        set_time_limit (500); // this is gonna take a while so make sure we don't have any issues with it..
        forEach($allPosts as $key1 => $post){
            $row = [$post];
            forEach($allPosts as $key2 => $otherPost){
                if($post[1]==$otherPost[1] && $post[2]!==$otherPost[2]){
                    array_push($row,$otherPost);
                    unset($allPosts[$key2]);
                }
            }
            unset($allPosts[$key1]);
            if(count($row) !== 1 && count($row) !== 2)
                $this -> insertRow($row);
        }
    }

    private function insertRow($row){
        $sqlColumns = "INSERT INTO evals(";
        $sqlValues = "VALUES(";
        forEach($row as $fieldNum){
            $sqlColumns.= '`'.$this->getColumnName($fieldNum[2]).'`, ';
            $unserializedVal = unserialize($fieldNum[3]);
            if(getType($unserializedVal) == "array")
                forEach($unserializedVal as $randomFuckingValue){
                    $sqlValues.= "'".$this->db_connection->real_escape_string($randomFuckingValue['file_url'])."', ";
                }
            else
                $sqlValues.= "'".$fieldNum[3]."', ";
        }
        $sqlColumns.= "progress)";
        $sqlValues.= "0);";
        $sql = $sqlColumns." ".$sqlValues;

        $this->db_connection->query($sql);
    }

    private function getColumnName($field){
        forEach($this->columnsArray as $column){
            if("_field_".$column['id'] == $field)
                return $column['name'];
        }
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

//DEPLOYMENT
//uncomment the next 2 lines and access the file by link to create and populate the evals table
$deployment = new EvalsAPI(6);
$deployment -> generateEvalTable();
?>
