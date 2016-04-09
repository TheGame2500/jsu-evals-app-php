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

    public function texter()
    {
        // delete the session of the user
        echo "VACO";

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
        echo "<form action='#'>";
        echo "<br> Nume: ". $a["nume"]. " Prenume: ". $a["prenume"]. "<br>";
        echo "<br> question1: ". $a["question1"]. "<br>";
        echo "Nota formular: <input id='nota-formular' type='text' class=''></input><br>";
        echo "<br> question2: ". $a["question2"]. "<br>";
        echo "Nota recomandare: <input id='nota-recomandare' type='text' class=''></input><br>";
        echo "<br> question3: ". $a["question3"]. "<br>";
        echo "Nota Voluntariat: <input id='nota-voluntariat' type='text' class=''></input><br>";
        echo "<input type='submit' value='Submit'>";
        echo "</form>";

    }

    public function updateProgress($a)
    {
		$id = $a['id'];
        if ($a["progress"] == 0)
        {
            $sql = "UPDATE evals SET progress = 1 WHERE id = $id AND progress = 0 LIMIT 1";
        } 
        elseif ($a["progress"] == 1)  
        {
            $sql = "UPDATE evals SET progress = 2 WHERE id = $id AND progress = 1 LIMIT 1";
        }


        if ($this->db_connection->query($sql) === TRUE) 
        {
            echo "New record created successfully";
        } 
        else 
        {
            echo "Error: " . $sql . "<br>" . $this->db_connection->error;
        }
    }

	/**
		Input: object containing the 3 required marks
		Output: Echo success/not success message
	**/
	public function submitMarks ($marks){
		if(!$this->validMarks($marks)){
			echo 'Note invalide, doar numere reale pozitive intre 1 si 10 sunt permise';
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
            echo "New record created successfully";
        } 
        else 
        {
            echo "Error: " . $sql . "<br>" . $this->db_connection->error;
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
