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
    $lookup = $_GET['lookup'] ?? '';  // 'cities' or empty

    // Common styling
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>World Lookup</title>
    <link rel='stylesheet' href='world.css'>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { width: 80%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1, p { text-align: center; }
        .no-results { text-align: center; font-style: italic; color: #666; }
    </style>
</head>
<body>
    <h1>World Database Lookup</h1>";

    if ($lookup === 'cities') {
        if ($country === '') {
            echo "<p class='no-results'>Please enter a country name to lookup cities.</p></body></html>";
            exit;
        }

        $stmt = $conn->prepare("SELECT ci.name AS city_name, ci.district, ci.population 
                                FROM cities ci 
                                JOIN countries co ON ci.country_code = co.code 
                                WHERE co.name LIKE ? 
                                ORDER BY ci.population DESC");
        $stmt->execute(["%$country%"]);

        echo "<p>Cities in countries matching: <strong>" . htmlspecialchars($country) . "</strong></p>";
        echo "<table>
            <thead><tr><th>City Name</th><th>District</th><th>Population</th></tr></thead>
            <tbody>";

        $found = false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $found = true;
            echo "<tr>
                    <td>" . htmlspecialchars($row['city_name']) . "</td>
                    <td>" . htmlspecialchars($row['district']) . "</td>
                    <td>" . number_format($row['population']) . "</td>
                  </tr>";
        }

        if (!$found) {
            echo "<tr><td colspan='3' class='no-results'>No cities found for '$country'</td></tr>";
        }

        echo "</tbody></table></body></html>";

    } else {
        // Country table (Exercise 4)
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

        echo ($country !== '' ? "<p>Countries matching: <strong>" . htmlspecialchars($country) . "</strong></p>" : "<p>All countries</p>");

        echo "<table>
            <thead><tr><th>Country Name</th><th>Continent</th><th>Independence Year</th><th>Head of State</th></tr></thead>
            <tbody>";

        $found = false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $found = true;
            echo "<tr>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['continent']) . "</td>
                    <td>" . ($row['independence_year'] ?: '—') . "</td>
                    <td>" . ($row['head_of_state'] ? htmlspecialchars($row['head_of_state']) : '—') . "</td>
                  </tr>";
        }

        if (!$found && $country !== '') {
            echo "<tr><td colspan='4' class='no-results'>No countries found</td></tr>";
        }

        echo "</tbody></table></body></html>";
    }

} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>