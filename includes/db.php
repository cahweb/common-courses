<?

function db_connect() {
    $db_config = include "db_config.php";

    $db_server = $db_config["server"];
    $db_user = $db_config["user"];
    $db_pass = $db_config["pass"];
    $db = $db_config["db"];
    $db_charset = 'utf-8';

    $db_connection = new mysqli($db_server, $db_user, $db_pass, $db);

    if ($db_connection -> connect_errno) {
        echo "Failed to connect to " . $db_server . ": " . $mysqli -> connect_error;
        exit();
    }

    $db_connection->set_charset($db_charset);

    return $db_connection;
}

function db_close($db_connection) {
    $db_connection->close();
}

function db_query($db_connection, $sql) {
    if ($result = $db_connection->query($sql)) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo("Mysqli Error: " . $result -> error . "<br>");
        return false;
    }
}

?>