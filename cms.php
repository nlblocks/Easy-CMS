<?php
// Include the configuration file
require_once 'cms-files/cms_config.php';

// Initialize variables
$loginError = '';
$dashboardContent = '';

// Start session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    $dashboardContent = "Welcome, " . $_SESSION['username'] . "!";
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $enteredUsername = $_POST['username'];
    $enteredPassword = $_POST['password'];

    // Check entered credentials
    if ($enteredUsername === $username && $enteredPassword === $password) {
        // Successful login
        $_SESSION['username'] = $enteredUsername;
        $dashboardContent = "Welcome, " . $_SESSION['username'] . "!";
    } else {
        // Failed login
        $loginError = "Invalid login credentials. Please try again.";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<?php if (!$dashboardContent) { ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Easy CMS - Easily manage your webpages.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        /* Custom style to center content */
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh; /* Set the container to take up the full viewport height */
        }
    </style>
</head>
<body class="w3-pale-blue">
        
        <div class="center-container w3-pale-blue">
            <div class="w3-card w3-padding w3-margin w3-white w3-center" style="max-width: 300px;">
                <h2>Login - Easy CMS</h2>
                <form class="w3-container" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <label for="username">Username:</label>
                    <input class="w3-input" type="text" id="username" name="username" required>

                    <label for="password">Password:</label>
                    <input class="w3-input" type="password" id="password" name="password" required>

                    <input class="w3-button w3-blue w3-margin-top" type="submit" value="Login">
                </form>
                <?php echo $loginError; ?>
            </div>
        
    </div>
</body>
</html>

<?php } else { ?>

<!DOCTYPE html>
<html>
<head>
    <title>Easy CMS - Easily manage your webpages.</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="w3-container w3-pale-blue" style="padding-bottom: 50px;">
        <h1 class="w3-center"><b>Easy CMS - Easily manage your webpages.</b></h1>
        <a href="?logout=true" class="w3-button w3-red w3-small w3-margin-top w3-margin-right" style="top: 0; right: 0; position: absolute;">Logout</a>
        <form class="w3-container" method="post" action="cms-files/cms_upload.php">
            <style>
                .max-height-200 {
                    max-height: 200px;
                }
                
                .w3-card-4 {
                    padding: 10px;
                    margin: 10px 2.5% 40px 2.5% !important;
                }
                
                hr {
                    border: 1px solid SkyBlue;
                }
                
                .hr-main {
                    border: 2px solid DeepSkyBlue !important;
                }
            </style>
            <?php

            include 'cms-files/cms_config.php';

            $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $currentDirectory = __DIR__;
            $files = scandir($currentDirectory);

            $pageElements = [];

            foreach ($files as $file) {
                if (
                    ($file !== '.' && $file !== '..') &&
                    (pathinfo($file, PATHINFO_EXTENSION) === 'php' || pathinfo($file, PATHINFO_EXTENSION) === 'html') &&
                    is_file($file)
                ) {
                    $fileContents = file_get_contents($file);
                    preg_match_all('/<div id=["\']CMS-(.*?)["\']>(.*?)<\/div>/s', $fileContents, $matches);

                    foreach ($matches[1] as $index => $id) {
                        $content = $matches[2][$index];

                        preg_match('/<h(\d+)>(.*?)<\/h\1>/', $content, $headerMatch);
                        preg_match('/<p>(.*?)<\/p>/', $content, $paragraphMatch);
                        preg_match('/<img.*?src=["\'](.*?)["\'].*?\/?>/', $content, $imageMatch);
                        preg_match('/<a.*?href=["\'](.*?)["\'].*?>(.*?)<\/a>/', $content, $linkMatch);

                        $pageName = $file;
                        if (!isset($pageElements[$pageName])) {
                            $pageElements[$pageName] = [];
                        }

                        $pageElements[$pageName][] = [
                            'id' => $id,
                            'header' => $headerMatch[2] ?? '',
                            'paragraph' => $paragraphMatch[1] ?? '',
                            'image' => $imageMatch[1] ?? '',
                            'link' => [
                                'href' => $linkMatch[1] ?? '',
                                'text' => $linkMatch[2] ?? '',
                            ],
                        ];
                    }
                }
            }

            foreach ($pageElements as $pageName => $elements) {
                echo '<div class="w3-card-4 w3-light-grey w3-margin">';
                echo '<h3 class="w3-center"><b> File: ' . $pageName . '</b></h3>';

                foreach ($elements as $element) {
                    echo '<div class="w3-container">';
                    echo '<hr class="hr-main">';
                    echo '<h4><b>ID: ' . $element['id'] . '</b></h4>';
                    
                    if (!empty($element['header'])) {
                        $result = $conn->query("SELECT id FROM Content WHERE divid = '".$element['id']."' and type = 'header'");
                        echo '<hr>';
                        if ($result->num_rows > 0) {
                            $content = $conn->query("SELECT content FROM Content WHERE divid = '".$element['id']."' and `type` = 'header' LIMIT 1")->fetch_object()->content;
                            echo '<label for="' . $element['id'] . '-header">Header:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-header" id="' . $element['id'] . '-header" value="' . $content . '"><br>';
                        } else {
                            echo '<label for="' . $element['id'] . '-header">Header:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-header" id="' . $element['id'] . '-header" value="' . htmlspecialchars($element['header']) . '"><br>';
                        }
                    }
                    
                    if (!empty($element['paragraph'])) {
                        $result = $conn->query("SELECT id FROM Content WHERE divid = '".$element['id']."' and type = 'paragraph'");
                        echo '<hr>';
                        if ($result->num_rows > 0) {
                            $content = $conn->query("SELECT content FROM Content WHERE divid = '".$element['id']."' and `type` = 'paragraph' LIMIT 1")->fetch_object()->content;
                            echo '<label for="' . $element['id'] . '-paragraph">Paragraph:</label><br>';
                            echo '<textarea class="w3-input w3-border" name="' . $element['id'] . '-paragraph" id="' . $element['id'] . '-paragraph">' . $content . '</textarea><br>';
                        } else {
                            echo '<label for="' . $element['id'] . '-paragraph">Paragraph:</label><br>';
                            echo '<textarea class="w3-input w3-border" name="' . $element['id'] . '-paragraph" id="' . $element['id'] . '-paragraph">' . htmlspecialchars($element['paragraph']) . '</textarea><br>';
                        }
                    }

                    if (!empty($element['image'])) {
                        $result = $conn->query("SELECT id FROM Content WHERE divid = '".$element['id']."' and type = 'image'");
                        echo '<hr>';
                        if ($result->num_rows > 0) {
                            $url = $conn->query("SELECT url FROM Content WHERE divid = '".$element['id']."' and `type` = 'image' LIMIT 1")->fetch_object()->url;
                            echo '<label for="' . $element['id'] . '-image">Image URL:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-image" id="' . $element['id'] . '-image" value="' . $url . '"><br>';
                            echo '<img class="max-height-200" src="' . $url . '" alt="Image"><br><br>';
                        } else {
                            echo '<label for="' . $element['id'] . '-image">Image URL:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-image" id="' . $element['id'] . '-image" value="' . htmlspecialchars($element['image']) . '"><br>';
                            echo '<img class="max-height-200" src="' . htmlspecialchars($element['image']) . '" alt="Image"><br><br>';
                        }
                    }

                    if (!empty($element['link']['href'])) {
                        $result = $conn->query("SELECT id FROM Content WHERE divid = '".$element['id']."' and type = 'link'");
                        echo '<hr>';
                        if ($result->num_rows > 0) {
                            $url = $conn->query("SELECT url FROM Content WHERE divid = '".$element['id']."' and `type` = 'link' LIMIT 1")->fetch_object()->url;
                            $content = $conn->query("SELECT content FROM Content WHERE divid = '".$element['id']."' and `type` = 'link' LIMIT 1")->fetch_object()->content;
                            echo '<label for="' . $element['id'] . '-link-href">Link Href:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-link-href" id="' . $element['id'] . '-link-href" value="' . $url . '"><br>';
    
                            echo '<label for="' . $element['id'] . '-link-text">Link Text:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-link-text" id="' . $element['id'] . '-link-text" value="' . $content . '"><br>';
                        } else {
                            echo '<label for="' . $element['id'] . '-link-href">Link Href:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-link-href" id="' . $element['id'] . '-link-href" value="' . htmlspecialchars($element['link']['href']) . '"><br>';
    
                            echo '<label for="' . $element['id'] . '-link-text">Link Text:</label><br>';
                            echo '<input class="w3-input w3-border" type="text" name="' . $element['id'] . '-link-text" id="' . $element['id'] . '-link-text" value="' . htmlspecialchars($element['link']['text']) . '"><br>';
                        }
                    }

                    echo '</div>';
                }

                echo '</div>';
            }
            
            $conn->close();
            ?>
            
            <button class="w3-button w3-blue" style="margin-left: 3%; margin-top: -20px; padding: 8px 40px; font-size: 20px; border: 4px solid DarkCyan;" type="submit">Submit</button>
        </form>
    </div>
</body>
</html>

<?php } ?>