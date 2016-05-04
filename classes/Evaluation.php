<?php

/**
 * Class Evaluation
 * handles the user's evaluation input
 */
require('../deploy/kint-master/Kint.class.php');
class Evaluation
{
    /**
     * @var object The database connection
     */
    private $db_connection = null;
    /**
     * @var array Collection of error messages
     */
    public $errors = array();
    /**
     * @var array Collection of success / neutral messages
     */
    public $messages = array();

    private $columns = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        session_start();
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        require_once('../deploy/evalsTable.php');
        $evalsAPI = new EvalsAPI(6);
        $this->columns = $evalsAPI -> getPrettyColumns(); 
    }

    public function randomGenerator()
    {
		$user_id = $_SESSION['user_id'];
        if (!$this->db_connection->connect_errno)
        {
            $sql = "SELECT id, 
                           Optiunea_1, Optiunea_2, Optiunea_3,
                           `1.Ce_te-ar_determina`,
                           `2._De_ce_ai_optat_pe`,
                           `3.Ce_te_recomandă_s`,
                           `4.Având_în_vedere_`,
                           `5.Menţionează_2-3_`,
                           `6.De_unde_ai_aflat_d`,
                           `Scrisoare_de_recoman`,
                           `Adeverința`,
                           `Acord_parental`
					FROM evals ev
					WHERE
					progress != 2
					and NOT EXISTS(
							SELECT *
							FROM marks
							WHERE user_id = $user_id
								AND form_id = ev.id
					) ORDER BY RAND() LIMIT 1";

            $result = $this->db_connection->query($sql);

            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();

				$_SESSION["form_id"] = $row["id"];
                $this->formGenerator($row);
                $this->updateProgress($row);

            }
            else
            {
                echo "Nu mai exista aplicatii de corectat";
            }
        }
    }

    //THIS IS NOT A FORM GENERATOR. THIS IS A FUCKING FORM AND THAT'S IT.
    //Gets the columns from the object property and prints them out.. Hopefully in a neat-ish way` 
    public function formGenerator($application)
    {
        echo "<form class='form jumbotron form-inline' action='#'>";
        forEach($application as $fieldName => $fieldValue){
            $prettyName = "";
            forEach($this->columns as $column){
                if( $fieldName == $column['name'] && $fieldName !== "id" ){
                    $prettyName = $column['prettyName'];
                    echo "<span><strong>".$prettyName."</strong> : <br> " .$fieldValue. "</span><br>";
                }
            }
        }
        echo "  <div class='form-group'>
                    <label for='nota-formular'>
                            Nota formular:
                    </label> 
                    <input id='nota-formular' name='nota-formular' type='text' class='form-control'>
                </div>";
        echo "  <div class='form-group'>
                    <label for='nota-recomandare'> 
                        Nota recomandare: 
                    </label> 
                    <input id='nota-recomandare' name='nota-recomandare' type='text' class='form-control'>
                </div>";
        echo "  <div class='form-group'>
                    <label for='nota-voluntariat'>
	    	        Nota Voluntariat: 
    	            </label>			  
                    <input id='nota-voluntariat' name='nota-voluntariat' type='text' class='form-control'>
                </div>";
        echo "<div class='form-group row col-xs-12'><button class='btn btn-success' type='submit'>Submit</button></div>";
        echo "</form>";
    }

    public function updateProgress($a)
    {
		$id = $a['id'];
		$sql = "UPDATE evals SET progress = progress + 1 WHERE id = $id";

        if (!$this->db_connection->query($sql)) 
        {
            $response['success']=false;
			$response['message']="'Error: " . $sql . "<br>" . $this->db_connection->error . ". <br> contacteaza-i pe Bira si Rares.'";
            echo json_encode($response);
        }
    }

	/**
		Input: object containing the 3 required marks
		Output: Echo success/not success message
	**/
	public function submitMarks ($marks){
		if(!$this->validMarks($marks)){
			$response['success']=false;
			$response['message']="Note invalide, doar numere reale pozitive intre 1 si 10 sunt permise";
			echo json_encode($response);
			return;
		}
		$notaFormular = mysqli_real_escape_string($this->db_connection,$marks['notaFormular']);
		$notaRecomandare = mysqli_real_escape_string($this->db_connection,$marks['notaRecomandare']);
		$notaVoluntariat= mysqli_real_escape_string($this->db_connection,$marks['notaVoluntariat']);
		$form_id = mysqli_real_escape_string($this->db_connection,$_SESSION['form_id']);
		$medie = number_format(($notaFormular * 0.45 + $notaRecomandare * 0.45 + $notaVoluntariat * 0.1),2);
		$user_id = mysqli_real_escape_string($this->db_connection,$_SESSION['user_id']);
		$user_name = mysqli_real_escape_string($this->db_connection,$_SESSION['user_name']);
		$sql = "INSERT INTO marks(nota_formular,nota_recomandare,nota_voluntariat,form_id,medie,user_id,user_name)
				VALUES($notaFormular,$notaRecomandare,$notaVoluntariat,$form_id,$medie,$user_id,'$user_name');";
		//clear it out of session, for purposes of rolling the progress back if user logs out without completing eval

		$_SESSION['form_id'] = null;	
        if ($this->db_connection->query($sql) === TRUE) 
        {
			$response['success']=true;
			$response['message']='Evaluare completata cu success';
            echo json_encode($response); 
        } 
        else 
        {
			$response['success']=false;
			$response['message']="'Error: " . $sql . "<br>" . $this->db_connection->error . ". <br> contacteaza-i pe Bira si Rares.'";
            echo json_encode($response); 
        }

	}

	private function validMarks($marks){
		$valid = true;
		foreach($marks as $val){
			$value = (float)$val;
			if(!($value >= 1 && $value <= 10 && filter_var($value, FILTER_VALIDATE_FLOAT)) )
				$valid = false;
		}
		return $valid;
	}

	public function rollbackProgress($form_id){
		$sql = "UPDATE evals SET progress = progress - 1 WHERE id = $form_id";
		$this->db_connection->query($sql);
	}
}
