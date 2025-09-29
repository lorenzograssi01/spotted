<?php
function connect()
{
    $servername = "localhost";
    $username = "root";
    
    $database = new mysqli($servername, $username);
    if ($database->connect_error)
    {
        die("Connection failed: " . $database->connect_error);
    }

    $query = "USE grassi_620944";
    $result = $database->query($query);

    return $database;
}
?>