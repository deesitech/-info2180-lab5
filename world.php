<?php
header('Content-Type: text/html;charset=UTF-8');

$host = 'localhost';
$dbname = 'world';
$username = 'lab5_user';
$password = 'password123';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $country = trim($_GET['country'] ?? '');

    if ($country !== '') {
        $stmt = $conn->prepare("SELECT name, continent, independence_year, head_of_state 
                                FROM countries 
                                WHERE name LIKE ? 
                                ORDER BY name");
        $stmt->execute(["%$country%"]);
    } else {
        $stmt = $conn->query("SELECT name, continent, independence_year, head_of_state 
                              FROM countries 
                              ORDER BY name");
    }

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>World Country Lookup</title>
    <link rel='stylesheet' href='world.css'>
    <style>
        table { width: 80%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1, p { text-align: center; }
    </style>
</head>
<body>
    <h1>Country Lookup Results</h1>";

    if ($country !== '') {
        echo "<p>Searching for: <strong>" . htmlspecialchars($country) . "</strong></p>";
    } else {
        echo "<p>Showing all countries</p>";
    }

    echo "<table>
        <thead>
            <tr>
                <th>Country Name</th>
                <th>Continent</th>
                <th>Independence Year</th>
                <th>Head of State</th>
            </tr>
        </thead>
        <tbody>";

    $found = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $found = true;
        $name = htmlspecialchars($row['name']);
        $continent = htmlspecialchars($row['continent']);
        $indep = $row['independence_year'] ? $row['independence_year'] : '—';
        $leader = $row['head_of_state'] ? htmlspecialchars($row['head_of_state']) : '—';

        echo "<tr>
                <td>$name</td>
                <td>$continent</td>
                <td>$indep</td>
                <td>$leader</td>
              </tr>";
    }

    if (!$found && $country !== '') {
        echo "<tr><td colspan='4'>No countries found matching '" . htmlspecialchars($country) . "'</td></tr>";
    }

    echo "    </tbody>
          </table>
</body>
</html>";

} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>