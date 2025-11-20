<?php
echo "<h2>🔍 MySQL Connection Diagnostic</h2>";

$host = "localhost";
$user = "kur.malual";
$pass = "kur_799";
$db   = "webtech_2025A_kur_malual";

/* STEP 1 — Test raw mysqli connection */
echo "<h3>1️⃣ Testing mysqli_connect...</h3>";
$conn = @mysqli_connect($host, $user, $pass);

if (!$conn) {
    echo "<p style='color:red;'>❌ mysqli_connect FAILED:</p>";
    echo "<pre>" . mysqli_connect_error() . "</pre>";
} else {
    echo "<p style='color:green;'>✅ mysqli_connect successful!</p>";
}

/* STEP 2 — Check if user can see databases */
echo "<h3>2️⃣ Checking SHOW DATABASES...</h3>";
if ($conn) {
    $result = mysqli_query($conn, "SHOW DATABASES");
    if ($result) {
        echo "<p style='color:green;'>✅ User can list databases.</p>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>" . $row['Database'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red;'>❌ Cannot list databases. Permission issue.</p>";
        echo "<pre>" . mysqli_error($conn) . "</pre>";
    }
}

/* STEP 3 — Try selecting your database */
echo "<h3>3️⃣ Testing database selection ($db)...</h3>";
if ($conn) {
    if (mysqli_select_db($conn, $db)) {
        echo "<p style='color:green;'>✅ Database found and accessible!</p>";
    } else {
        echo "<p style='color:red;'>❌ Database cannot be selected:</p>";
        echo "<pre>" . mysqli_error($conn) . "</pre>";
    }
}

/* STEP 4 — Test PDO connection */
echo "<h3>4️⃣ Testing PDO connection...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p style='color:green;'>✅ PDO connected successfully!</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ PDO ERROR:</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

echo "<hr><p>✔ Diagnostic complete.</p>";
?>
