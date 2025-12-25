<?php
// world.php - Exercise 2: Connect to DB and list all countries with head of state

$host = 'localhost';
$dbname = 'world';
$username = 'lab5_user';
$password = 'password123';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query all countries
    $stmt = $conn->query("SELECT name, head_of_state FROM countries ORDER BY name");

    echo "<!DOCTYPE html>\n";
    echo "<html lang='en'>\n";
    echo "<head>\n";
    echo "  <meta charset='UTF-8'>\n";
    echo "  <title>World Countries</title>\n";
    echo "  <link rel='stylesheet' href='world.css'>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "  <h1>Countries and Heads of State</h1>\n";
    echo "  <ul>\n";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name = htmlspecialchars($row['name']);
        $leader = htmlspecialchars($row['head_of_state'] ?? '—');
        echo "    <li><strong>$name</strong> – $leader</li>\n";
    }

    echo "  </ul>\n";
    echo "</body>\n";
    echo "</html>\n";

} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 