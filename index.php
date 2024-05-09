<?php
echo '<pre>' . print_r($_POST, true) . '</pre>';

$host = 'localhost';
$db   = 'ifoa_firstdatabase';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Comando che connette al database
$pdo = new PDO($dsn, $user, $pass, $options);

// Funzione per aggiungere i dati dal CSV al database
// Funzione per aggiungere i dati dal CSV al database
function importaDaCSV($pdo, $csvFile) {
    // Apri il file CSV in modalità lettura
    $handle = fopen($csvFile, 'r');

    // Se il file CSV è stato aperto correttamente
    if ($handle !== false) {
        // Loop per leggere ogni riga del CSV
        while (($row = fgetcsv($handle)) !== false) {
            // Rimuovi il primo elemento (id) dall'array $row
            array_shift($row); // Rimuovi il primo elemento dall'array

            // Verifica se esiste già una riga nel database con gli stessi dati
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM listautenti WHERE Username = ? AND Email = ? AND age = ? AND Password = ?");
            $stmt->execute($row);
            $rowCount = $stmt->fetchColumn();

            // Se non esiste una riga con gli stessi dati, esegui l'inserimento
            if ($rowCount == 0) {
                // Esegui l'operazione di inserimento dei dati nel database
                $stmt = $pdo->prepare("INSERT INTO listautenti (Username, Email, age, Password) VALUES (?, ?, ?, ?)");
                $stmt->execute($row); // Passa solo i valori relativi a Username, Email, age e Password
            }
        }

        // Chiudi il file CSV
        fclose($handle);

        echo "Dati importati dal CSV al database con successo!";
    } else {
        echo "Impossibile aprire il file CSV.";
    }
}



// Utilizza la funzione per importare i dati dal CSV al database
importaDaCSV($pdo, './listautenti.csv');

?>
