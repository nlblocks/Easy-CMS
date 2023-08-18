<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["div_id"])) {
        $div_ids = $_POST["div_id"];

        include 'cms_config.php';

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $data = array();

        foreach ($div_ids as $div_id) {
            $sql = "SELECT type, content, url FROM Content WHERE divid = '$div_id'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row["type"] === "header") {
                        $data[$div_id]["header"] = $row["content"];
                    } elseif ($row["type"] === "paragraph") {
                        $data[$div_id]["paragraph"] = $row["content"];
                    } elseif ($row["type"] === "link") {
                        // If the row has text content
                        if (isset($row["content"])) {
                            $data[$div_id]["link"] = array(
                                "url" => $row["url"],
                                "text" => $row["content"]
                            );
                        } else {
                            $data[$div_id]["link"] = $row["url"];
                        }
                    } elseif ($row["type"] === "image") {
                        $data[$div_id]["image"] = $row["url"];
                    }
                }
            }
        }

        $conn->close();

        echo json_encode($data);
    }
}
?>
