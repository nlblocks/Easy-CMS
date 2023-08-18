<?php

include 'cms_config.php';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $field => $content) {
        $content = mysqli_real_escape_string($conn, $content); // Escape and sanitize content
        
        if (preg_match('/^(.+?)-(.+)$/', $field, $matches)) {
            $divid = $matches[1];
            $type = $matches[2];

            if ($type === "link-href") {
                // Update or insert link record with href
                $query = "SELECT id FROM Content WHERE divid = '$divid' AND type = 'link'";
                $result = $conn->query($query);

                $url = mysqli_real_escape_string($conn, $_POST["$divid-link-href"]); // Escape and sanitize URL
                if ($result->num_rows > 0) {
                    // Update the existing link record's URL
                    $updateQuery = "UPDATE Content SET url = '$url' WHERE divid = '$divid' AND type = 'link'";
                    $conn->query($updateQuery);
                } else {
                    // Insert a new link record with URL
                    $insertQuery = "INSERT INTO Content (divid, type, url) VALUES ('$divid', 'link', '$url')";
                    $conn->query($insertQuery);
                }
            } elseif ($type === "link-text") {
                // Update or insert link record with text
                $query = "SELECT id FROM Content WHERE divid = '$divid' AND type = 'link'";
                $result = $conn->query($query);

                $linkText = mysqli_real_escape_string($conn, $_POST["$divid-link-text"]); // Escape and sanitize link text
                if ($result->num_rows > 0) {
                    // Update the existing link record's content
                    $updateQuery = "UPDATE Content SET content = '$linkText' WHERE divid = '$divid' AND type = 'link'";
                    $conn->query($updateQuery);
                } else {
                    // Insert a new link record with content
                    $insertQuery = "INSERT INTO Content (divid, type, content) VALUES ('$divid', 'link', '$linkText')";
                    $conn->query($insertQuery);
                }
            } elseif ($type === "paragraph" || $type === "header" || $type === "image") {
                // Update or insert record for paragraph, header, or image
                $query = "SELECT id FROM Content WHERE divid = '$divid' AND type = '$type'";
                $result = $conn->query($query);

                if ($type === "image") {
                    $url = mysqli_real_escape_string($conn, $_POST["$divid-image"]); // Escape and sanitize image URL
                    if ($result->num_rows > 0) {
                        // Update the existing image record's URL
                        $updateQuery = "UPDATE Content SET url = '$url' WHERE divid = '$divid' AND type = 'image'";
                        $conn->query($updateQuery);
                    } else {
                        // Insert a new image record with URL
                        $insertQuery = "INSERT INTO Content (divid, type, url) VALUES ('$divid', 'image', '$url')";
                        $conn->query($insertQuery);
                    }
                } else {
                    if ($result->num_rows > 0) {
                        // Update the existing record's content
                        $updateQuery = "UPDATE Content SET content = '$content' WHERE divid = '$divid' AND type = '$type'";
                        $conn->query($updateQuery);
                    } else {
                        // Insert a new record with content
                        $insertQuery = "INSERT INTO Content (divid, type, content) VALUES ('$divid', '$type', '$content')";
                        $conn->query($insertQuery);
                    }
                }
            }
        }
    }
}

$conn->close();
header("Location: ../cms.php");
?>
