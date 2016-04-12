<?php

/**
 * Class Evaluation
 * handles the user's evaluation input
 */
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

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        session_start();
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    public function randomGenerator()
    {
		$user_id = $_SESSION['user_id'];
        if (!$this->db_connection->connect_errno)
        {
            $sql = "SELECT *
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

    public function formGenerator($a)
    {
        echo "<form class='form jumbotron' action='#'>";
        echo "<span> Nume: ". $a["nume"]. " Prenume: ". $a["prenume"]. "</span><br>";
        echo "<span> question1: ". $a["question1"]. "</span><br>";
        echo "<label for='nota-formular'>
				Nota formular:
			  </label> 
			  <input id='nota-formular' name='nota-formular' type='text' class='form-control'></input><br>";
        echo "<span> question2: ". $a["question2"]. "<br>";
        echo "<label for='nota-recomandare'> 
				Nota recomandare: 
			  </label> 
			  <input id='nota-recomandare' name='nota-recomandare' type='text' class='form-control'></input><br>";
        echo "<span> question3: ". $a["question3"]. "</span><br>";
        echo "<label for='nota-voluntariat'
				Nota Voluntariat: 
			  <label>
			  <input id='nota-voluntariat' name='nota-voluntariat' type='text' class='form-control'></input><br>";
        echo "<button class='btn btn-success' type='submit'>Submit</button>";
        echo "</form>";
    }

    public function updateProgress($a)
    {
		$id = $a['id'];
		$sql = "UPDATE evals SET progress = progress + 1 WHERE id = $id  LIMIT 1";

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
}
