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

        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    public function randomGenerator()
    {
        if (!$this->db_connection->connect_errno)
        {
            $sql = "SELECT * FROM evals /*WHERE progress!=2*/ ORDER BY RAND() LIMIT 1";
            $result = $this->db_connection->query($sql);

            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();

                $this->formGenerator($row);
                //$this->updateProgress($row);

            }
            else
            {
                echo "Nu mai exista aplicatii de corectat";
            }
        }
    }

    public function formGenerator($a)
    {
        echo "<form method='post' action='#' onsubmit='return false;' >";
        echo "<br> Nume: ". $a["nume"]. " Prenume: ". $a["prenume"]. "<br>";
        echo "<br> question1: ". $a["question1"]. "<br>";
        echo "Nota 1: <input type='text' class=''></input><br>";
        echo "<br> question2: ". $a["question2"]. "<br>";
        echo "Nota 2: <input type='text' class=''></input><br>";
        echo "<br> question3: ". $a["question3"]. "<br>";
        echo "Nota 3: <input type='text' class=''></input><br>";
        echo "<input type='button' onclick='xajax_tester();' value='Submit'>";
        echo "<p id='answer'></p>";
        echo "</form>";


    }

    public function updateProgress($a)
    {
        if ($a["progress"] == 0)
        {
            $sql = "UPDATE evals SET progress = 1 WHERE progress = 0 LIMIT 1";
        } 
        elseif ($a["progress"] == 1)  
        {
            $sql = "UPDATE evals SET progress = 2 WHERE progress = 1 LIMIT 1";
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

    public function tester()
    {
        echo "VACO";
    }
}
