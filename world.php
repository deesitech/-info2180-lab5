<?php
header('Content-Type: text/html;charset=UTF-8');

$host = 'localhost';
$dbname = 'world';
$username = 'lab5_user';
$password = 'password123';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the country from URL parameter, default to empty
    $country = trim($_GET['country'] ?? '');

    // Build the query
    if ($country !== '') {
        $stmt = $conn->prepare("SELECT name, head_of_state FROM countries WHERE name LIKE ? ORDER BY name");
        $stmt->execute(["%$country%"]);
    } else {
        $stmt = $conn->query("SELECT name, head_of_state FROM countries ORDER BY name");
    }

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>World Country Lookup</title>
    <link rel='stylesheet' href='world.css'>
</head>
<body>
    <h1>Country Lookup Results</h1>";

    if ($country !== '') {
        echo "<p>Searching for: <strong>" . htmlspecialchars($country) . "</strong></p>";
    } else {
        echo "<p>Showing all countries</p>";
    }

    echo "<ul>";

    $found = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $found = true;
        $name = htmlspecialchars($row['name']);
        $leader = $row['head_of_state'] ? htmlspecialchars($row['head_of_state']) : '—';
        echo "<li><strong>$name</strong> – $leader</li>";
    }

    if (!$found && $country !== '') {
        echo "<li>No countries found matching '$country'</li>";
    }

    echo "</ul>
</body>
</html>";

} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>