<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database extends SQLite3 {
    // Konstruktor für Verbindung zur DB
    public function __construct(){
        $this->open("/Applications/XAMPP/xamppfiles/htdocs/Mac/PortfolioWebprogII/PortfolioWebprogII.db");
    }

    // Methode um Student zur DB hinzuzufügen
    public function writeStudentToDB($student) {
        // In Tabelle Student werden Attribute hinzugefügt, VALUES werden per Getter-Methode aus Instanz geholt und in Tablle eingetragen
        $sql = "INSERT INTO Student (first_name, last_name, mtr, email, pw) VALUES (
            '{$student->getFirstName()}',
            '{$student->getLastName()}',
            {$student->getMtr()},
            '{$student->getEmail()}',
            '{$student->getPw()}')";

        // Die SQL-Anweisung ausführen und das Ergebnis speichern
        $result = $this->exec($sql);

        // Logik, um zu prüfen ob SQL-Abfrage erfolgreich war
        if ($result) {
            echo "Student was added!";
        } else {
            // Fehlerfall: Ausgabe der letzten Fehlermeldung. lastErrorMsg() ist Methode, von SQLite3-Klasse die in PHP bereitgestellt wird
            echo "Error: " . $this->lastErrorMsg(); 
        }
    }
    //Professor hinzufügen
    public function writeLecturerToDB($lecturer){

        $sql = "INSERT INTO Lecturer (first_name, last_name, pw) VALUES (
            '{$lecturer->getFirstName()}',
            '{$lecturer->getLastName()}',
            '{$lecturer->getPw()}')";

        $result = $this->exec($sql);

        // Logik, um zu prüfen ob SQL-Abfrage erfolgreich war
        if ($result) {
            echo "Lecturer added!";
        } else {
            // Fehlerfall: Ausgabe der letzten Fehlermeldung. lastErrorMsg() ist Methode, von SQLite3-Klasse die in PHP bereitgestellt wird
            echo "Error: " . $this->lastErrorMsg(); 
        }
    }

    //Slot hinzufügen
    public function writeSlotToDB($slot){

        $sql = "INSERT INTO consultation_slot (booker, duration, start_time, end_time, lecturer, status) VALUES (
            '{$slot->getBooker()}',
            '{$slot->getDuration()}',
            '{$slot->getStartTime()}',
            '{$slot->getEndTime()}',
            '{$slot->getLecturer()}',
            '{$slot->getStatus()}'

        )";
    
        $result = $this->exec($sql);
    
        // Logik, um zu prüfen ob SQL-Abfrage erfolgreich war
        if ($result) {
            echo "Slot added!";
        } else {
            // Fehlerfall: Ausgabe der letzten Fehlermeldung. lastErrorMsg() ist Methode, von SQLite3-Klasse die in PHP bereitgestellt wird
            echo "Error: " . $this->lastErrorMsg(); 
        }
    }

    // Methode, um Slots basierend auf Dozenten-ID abzurufen
    public function getSlotsByLecId($lecId) {
        $slots = array();
        $sql = "SELECT * FROM consultation_slot WHERE lecturer = $lecId";
        // SQL-Abfrage in der Datenbank, speichert Ergebnis in Variable $result
        $result = $this->query($sql);

        // Logik, um das Ergebnis zu verarbeiten
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $slot = new ConsultationSlot($row['booker'], $row['duration'], $row['start_time'], $row['end_time'], $row['lecturer']);
            $slots[] = $slot;
        }

        // Die Liste der Slots zurückgeben
        return $slots;
    }

    // Methode, um zu schauen ob Slot existiert
    public function checkSlotExists($slotID){
        // Aggregatfunktion : Count(), zählt Anzahl der Zeilen, wo SlotID = id
        $sql = "SELECT COUNT(*) FROM consultation_slot WHERE id = $slotID";
        $result = $this->querySingle($sql); // einzelne Spalte aus DB abrufen: id vergleichen ob diese grösser 0 ist
        // falls ja, dann True ansonsten False!
        return $result > 0;

    }

     // Methode, um zu überprüfen, ob ein Booker existiert
    private function checkBookerExists($bookerID) {
        $sql = "SELECT COUNT(*) FROM student WHERE id = $bookerID";
        $result = $this->querySingle($sql);

        return $result > 0;
    }

    // Methode, um zu überprüfen, ob ein Slot bereits reserviert ist
    private function checkSlotReserved($slotID) {
        $sql = "SELECT COUNT(*) FROM consultation_slot WHERE status = 'reserved' AND id = $slotID";
        $result = $this->querySingle($sql);

        return $result > 0;
    }

    // Methode, um einen Slot zu buchen (Reservierung)
    public function reserveSlot($slotID, $bookerID) {
        // Überprüfen, ob der Slot existiert
        $slotExists = $this->checkSlotExists($slotID);
    //not-operator, prueft ob slotExists false ist
        if (!$slotExists) {
            echo "Given Slot id not found";
            return false;
        }

        // Überprüfen, ob der Student existiert
        $bookerExists = $this->checkBookerExists($bookerID);

        if (!$bookerExists) {
            echo "Given Student id not found";
            return false;
        }

        // Überprüfen, ob der Slot bereits reserviert ist
        $isReserved = $this->checkSlotReserved($slotID);

        if ($isReserved) {
            echo "Given slot is already booked";
            return false;
        }

        // Reservierung durchführen, nur wo Ids stimmen!
        $sql = "UPDATE consultation_slot SET status = 'reserved', booker = $bookerID WHERE id = $slotID";

        // Die SQL-Anweisung ausführen und das Ergebnis speichern
        $result = $this->exec($sql);

        // Logik, um zu prüfen ob SQL-Abfrage erfolgreich war
        if ($result) {
            return true; // Reservierung erfolgreich
        } else {
            // Fehlerfall: Ausgabe der letzten Fehlermeldung
            echo "Error: " . $this->lastErrorMsg();
            return false; // Reservierung fehlgeschlagen
        }
    }

}



class Student {
    //Instanzvariabelen
    private $firstName;
    private $lastName;
    private $mtr;
    private $email;
    private $pw;

    public function __construct($firstName, $lastName, $mtr, $email, $pw) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->mtr = $mtr;
        $this->email = $email;
        $this->pw = $pw;
    }

    public function pw_isvalid() {
        //prueft ob mind. ein Großbuchstabe (A-Z) enthalten ist und strlen() prueft, ob Länge mind 6 ist
        return (preg_match('/[A-Z]/', $this->pw) && strlen($this->pw) >= 6);
    }
    // Getter Methode weil private und um Daten aus der Instanz aus test1.php zu bekommen, Setter nicht benötigt!
    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getMtr() {
        return $this->mtr;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPw() {
        return $this->pw;
    }
}

class Lecturer {
    //Instanzvariabelen
    private $firstName;
    private $lastName;
    private $pw;

    public function __construct($firstName, $lastName, $pw) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->pw = $pw;
    }

    public function pw_isvalid() {
        //prueft ob mind. ein Großbuchstabe (A-Z) enthalten ist und strlen() prueft, ob Länge mind 6 ist
        return (preg_match('/[A-Z]/', $this->pw) && strlen($this->pw) >= 6);
    }
    // Getter Methode weil private und um Daten aus der Instanz aus test1.php zu bekommen, Setter nicht benötigt!
    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getPw() {
        return $this->pw;
    }
}

class ConsultationSlot {
    //Instanzvariabelen
    private $booker;
    private $duration;
    private $start_time;
    private $end_time;
    private $lecturer;
    private $status;

    public function __construct($booker, $duration, $start_time, $end_time, $lecturer) {
        // $this ist  Referenz die dafür sorgt, dass man auf Instanzvariablen zugreifen kann
        // $status hat einen Standardwert ("available"), deshalb muss er nicht uebergeben werden
        $this->booker = $booker;
        $this->duration = $duration;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->lecturer = $lecturer;
        $this->status = "available";
    }

    public function getBooker() {
        return $this->booker;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function getStartTime() {
        return $this->start_time;
    }

    public function getEndTime() {
        return $this->end_time;
    }

    public function getLecturer() {
        return $this->lecturer;
    }

    public function getStatus() {
        return $this->status;
    }

    // show() Methode, um den Slot anzuzeigen
    public function show() {
        echo "Consultation Slot:</br>";
        echo "<ul>";
        echo "<li>Booker: {$this->booker}</li>";
        echo "<li>Duration: {$this->duration}</li>";
        echo "<li>Start Time: {$this->start_time}</li>";
        echo "<li>End Time: {$this->end_time}</li>";
        echo "<li>Lecturer: {$this->lecturer}</li>";
        echo "<li>Status: {$this->status}</li>";
        echo "</ul>";
        echo "<hr>";
    }

}


?>