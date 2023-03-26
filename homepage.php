<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Function to generate a random IV of the appropriate length
function generateIV()
{
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    return openssl_random_pseudo_bytes($ivLength);
}

// Function to encrypt the file contents with a given key and IV
function encryptFile($file, $key)
{
    // Open the file
    $fileHandle = fopen($file, 'r');
    $fileContents = fread($fileHandle, filesize($file));
    fclose($fileHandle);

    // Generate a random IV
    $iv = generateIV();

    // Encrypt the file contents with the key and IV
    $encryptedContents = openssl_encrypt($fileContents, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

    // Combine the IV and encrypted contents into a single string
    $result = $iv . $encryptedContents;

    return $result;
}

// Function to decrypt the file contents with a given key and IV
function decryptFile($fileContents, $key)
{
    // Extract the IV and encrypted contents from the input string
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($fileContents, 0, $ivLength);
    $encryptedContents = substr($fileContents, $ivLength);

    // Decrypt the file contents with the key and IV
    $decryptedContents = openssl_decrypt($encryptedContents, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

    if ($decryptedContents === false) {
        throw new Exception('Incorrect password');
    }

    return $decryptedContents;
}




// Connect to the database
$db = new mysqli('localhost', 'root', 'shreya16', 'db_connect');
if ($db->connect_errno) {
    die('Failed to connect to the database: ' . $db->connect_error);
}


// Check for upload button click
if (isset($_POST['upload'])) {
    // Get the uploaded file
    $file = $_FILES['file']['tmp_name'];
    $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);


    // Get the encryption key
    $key = $_POST['key'];

    // Hash the password
    $hashedPassword = password_hash($key, PASSWORD_DEFAULT);

    // Encrypt the file contents
    $encryptedContents = encryptFile($file, $key);

    $stmt = $db->prepare("INSERT INTO files (user_id, file_name, file_content, file_type, file_pass) VALUES (?, ?, ?, ?,?)");
    if (!$stmt) {
        die('Error in preparing the SQL statement: ' . $db->error);
    }

    $filename = $_FILES['file']['name'];

    $stmt->bind_param("issss", $_SESSION['id'], $filename, $encryptedContents, $fileType, $hashedPassword);
    if ($stmt->execute()) {
        $_SESSION['encrypt_success'] =  'File uploaded successfully.';
        echo "<script type='text/javascript'>alert('File uploaded successfully.')</script>";
    } else {
        $_SESSION['encrypt_fail'] =  'File uploaded successfully.';
        'Error: ' . $stmt->error;
    }

    header('Location: homepage.php');
    // Redirect to the home page
    exit();
}

// Check for decrypt button click
if (isset($_POST['decrypt'])) {
    // Get the search term
    $search = $_POST['search'];
    if (empty($search)) {
        $_SESSION['decrypt_error'] = 'Please provide a search term';
        exit();
    }

    // Get the decryption key
    $key = $_POST['key'];

    // Search for the file in the database
    $stmt = $db->prepare("SELECT * FROM files WHERE user_id = ? AND file_name LIKE ?");
    $searchValue = '%' . $search . '%';
    $stmt->bind_param("is", $_SESSION['id'], $searchValue);

    $stmt->execute();
    $result = $stmt->get_result();

    // Loop through the search results
    while ($row = $result->fetch_assoc()) {
        // Decrypt the file contents
        try {
            $decryptedContents = decryptFile($row['file_content'], $key);
        } catch (Exception $e) {
            $_SESSION['decrypt_error'] = 'Error: Incorrect password for file "' . $row['file_name'] . '".';
            continue;
        }

        // Save the decrypted file contents to a temporary file
        $tempFile = tmpfile();
        fwrite($tempFile, $decryptedContents);

        // Get the file extension
        $fileExtension = pathinfo($row['file_name'], PATHINFO_EXTENSION);

        // Set the appropriate content type header based on file extension
        switch ($fileExtension) {
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'doc':
            case 'docx':
                header('Content-Type: application/msword');
                break;
            case 'xls':
            case 'xlsx':
                header('Content-Type: application/vnd.ms-excel');
                break;
                // add more cases for other file types as needed
            default:
                header('Content-Type: application/octet-stream');
        }

        // Set the content disposition header to force download
        header('Content-Disposition: attachment; filename="' . $row['file_name'] . '"');

        // Output the decrypted file contents to the user's browser
        echo $decryptedContents;
    }

    // Stop further execution of the script
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>File Storage System</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #F5F5F5;
        margin: 0;
    }

    .navtop {
        background: linear-gradient(45deg, #2c3e50, #3498db);
        overflow: hidden;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 9999;
    }

    .navtop div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 80px;
        margin: 0 auto;
        max-width: 1000px;
        padding: 0 20px;
    }

    .navtop h1 {
        font-size: 28px;
        color: #fff;
        margin: 0;
        padding: 0;
        flex: 1;
    }

    .navtop a {
        font-size: 18px;
        color: #fff;
        text-decoration: none;
        padding: 0 20px;
        flex: 0;
    }

    .navtop a:hover {
        color: whitesmoke;
        background-color: lightslategray;
        height: 100%;
    }

    @media screen and (max-width: 600px) {

        .navtop a:not(:first-child),
        .navtop a:nth-last-child(2) {
            display: none;
        }

        .navtop a.icon {
            float: right;
            display: block;
        }
    }

    .navtop a i {
        padding-right: 5px;
    }

    .content p {
        width: 90%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        margin-top: 20px;
        box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
        margin: 25px 0;
        padding: 25px;
        background-color: #fff;

    }

    .upload-container,
    .search-container {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
        width: 70%;
    }

    .upload-container h2,
    .search-container h2 {
        margin-top: 0;
    }

    .upload-container label,
    .search-container input[type="text"],
    .upload-form input[type="password"],
    .search-form input[type="password"],
    .upload-form button,
    .search-form button {
        display: block;
        width: 100%;
        margin-bottom: 10px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .upload-container label {
        text-align: center;
        cursor: pointer;
        background-color: #337ab7;
        color: #fff;
        border: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .upload-container label:hover {
        background-color: #2e6da4;
    }

    .upload-form {
        width: 40%;
    }

    .search-form {
        width: 40%;
    }

    .search-container input[type="text"] {
        width: 100%;
        float: left;
    }

    .search-container button {
        width: 100px;
        float: right;
    }

    .filename {
        font-weight: bold;
        color: darkcyan;
    }


    @media screen and (max-width: 600px) {

        .navbar a:not(:first-child),
        .dropdown {
            display: none;
        }

        .navbar a.icon {
            float: right;
            display: block;
        }
    }

    form {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        margin-top: 20px;
        box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
        margin: 25px 0;
        padding: 25px;
        background-color: #fff;
    }

    form input[type="file"] {
        border: 2px dotted #333;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    form input[type="password"] {
        padding: 10px;
        margin-bottom: 10px;
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    form input[type="text"] {
        padding: 10px;
        margin-bottom: 10px;
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    form button[type="submit"] {
        padding: 10px;
        background-color: #3498db;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    form button[type="submit"]:hover {
        background-color: #666;
    }

    form input:focus,
    form button:focus {
        outline: none;
        border-color: #333;
    }

    .content {
        align-items: flex-start;
    }


    @media screen and (max-width: 600px) {
        .navbar.responsive .icon {
            position: absolute;
            right: 0;
            top: 0;
        }

        .navbar.responsive a {
            float: none;
            display: block;
            text-align: left;
        }

        .navbar.responsive .dropdown {
            display: block;
        }
    }
</style>



<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1>File Storage System</h1>
            <div>
                <a href="homepage.php"><i class="fas fa-home"></i>Home</a>
                <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="content">
        <h2>Home Page</h2>
        <p>Welcome back, <?= $_SESSION['username'] ?>!</p>
        <br>
        <h3>Upload your Document</h3>
        <form method="post" enctype="multipart/form-data" class="upload-form">
            <div class="upload-container">
                <label for="file-input"><i class="fas fa-cloud-upload-alt"></i> Upload File</label>
                <input id="file-input" type="file" name="file">
            </div>
            <input type="password" name="key" placeholder="Unique file key" required>
            <button type="submit" name="upload">Upload</button>
            <?php
            if (isset($_SESSION['encrypt_success'])) {
                echo "<div id='myAlert' class='alert alert-danger'>" . $_SESSION['encrypt_success'] . "</div>";
                unset($_SESSION['encrypt_success']);
            }

            if (isset($_SESSION['encrypt_fail'])) {
                echo "<div id='myAlert' class='alert alert-danger'>" . $_SESSION['encrypt_fail'] . "</div>";
                unset($_SESSION['encrypt_fail']);
            }
            ?>

            <script>
                // Remove alert after 5 seconds
                setTimeout(function() {
                    document.getElementById('myAlert').remove();
                }, 4000);
            </script>



        </form>
        <br>
        <br>
        <h3>Download your Document</h3>
        <form method="post" class="search-form">
            <div class="search-container">
                <input type="text" id="search" name="search" placeholder="Search file..." required>
                <div id="file-list"></div>

                <input id="key-input" type="password" name="key" placeholder="Key" required>
            </div>
            <button type="submit" name="decrypt">Fetch File</button>
        </form>
    </div>
</body>
<script>
    window.addEventListener("pageshow", function(event) {
        var historyTraversal = event.persisted ||
            (typeof window.performance != "undefined" &&
                window.performance.navigation.type === 2);
        if (historyTraversal) {
            // User clicked on back button, so log them out
            window.location.href = "logout.php";
        }
    });


    // Attach an event listener to the search file input field
    document.getElementById("search").addEventListener("input", function() {
        // Get the search term from the input field
        var search = this.value;

        // Make an AJAX request to fetch the list of files from the server
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Parse the response as a JSON object
                var fileList = JSON.parse(this.responseText);

                // Display the list of files in a dropdown menu
                var fileListHTML = "";
                for (var i = 0; i < fileList.length; i++) {
                    fileListHTML += "<div class='file' onclick='selectFile(\"" + fileList[i] + "\")'><span class='filename'>" + fileList[i] + "</span></div>";
                }
                document.getElementById("file-list").innerHTML = fileListHTML;

            }
        };
        xhr.open("GET", "get_files.php?search=" + search, true);
        xhr.send();
    });

    // Function to select a file from the dropdown menu
    function selectFile(filename) {
        // Fill the search file input field with the selected file name
        document.getElementById("search").value = filename;

        // Hide the dropdown menu
        document.getElementById("file-list").innerHTML = "";
    }
</script>

</html>