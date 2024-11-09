<?php
include("connect.php");

// Initialize message variable
$message = "";

// CHECK IF THE BUTTON WAS CLICKED
if (isset($_POST['btnSubmitPost'])) {
    $content = $_POST['content'];
    $attachment = $_FILES['attachment']['name'];

    // Move uploaded file to the 'uploads' directory if an attachment exists
    if ($attachment) {
        move_uploaded_file($_FILES['attachment']['tmp_name'], "uploads/" . $attachment);
    }

    // Prepare the SQL query
    $postQuery = "INSERT INTO posts (content, dateTime, attachment, userID, isDeleted) 
                  VALUES ('$content', NOW(), '$attachment', '6', 'no')";

    // Execute the query and set message
    if (mysqli_query($conn, $postQuery)) {
        $message = "Successfully posted!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Fetch posts that are not deleted, along with user information
$sql = "
    SELECT p.content, p.dateTime, p.attachment, u.username, ui.firstname, ui.lastname
    FROM posts p
    JOIN users u ON p.userID = u.userID
    JOIN userInfo ui ON u.userInfoID = ui.userInfoID
    WHERE p.isDeleted = 'no'
    ORDER BY p.dateTime DESC
";
$result = mysqli_query($conn, $sql);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Message Mate || Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        @font-face {
            font-family: 'Transforma';
            src: url('transforma-font/transforma-font.otf') format('opentype');
        }

        body {
            background-color: #f0f2f5;
            transition: background-color 0.3s, color 0.3s;
        }

        .container-fluid {
            background-color: #fff;
            padding: 10px;
            text-align: center;
        }

        .h1 {
            font-family: 'Transforma', sans-serif;
        }

        .feed-container {
            max-width: 600px;
            margin: 20px auto;
        }

        .post-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .post-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .post-card img {
            width: 100%;
            height: auto;
            border-radius: 8px 8px 0 0;
        }

        .post-content {
            padding: 15px;
        }

        .post-date {
            font-size: 0.9em;
            color: #777;
        }

        .post-author {
            font-weight: bold;
            color: #333;
            transition: color 0.3s;
        }

        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <div class="container-fluid shadow mb-5">
        <p class="h1 m-3">Message Mate</p>
    </div>

    <!-- Notification Message -->
    <?php if ($message): ?>
        <div class="notification" id="notification">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Posting Form Container -->
    <div class="container feed-container mb-4">
        <form class="mb-4 p-3 bg-light rounded shadow" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="content" class="form-label">What's on your mind?</label>
                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="attachment" class="form-label">Attach an image (optional)</label>
                <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*">
            </div>
            <button type="submit" name="btnSubmitPost" class="btn btn-primary">Post</button>
        </form>
    </div>

    <div class="container feed-container">
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($post = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12">
                        <div class="post-card">
                            <?php if ($post["attachment"]): ?>
                                <img src="uploads/<?php echo htmlspecialchars($post["attachment"]); ?>" alt="Post Image">
                            <?php endif; ?>
                            <div class="post-content">
                                <h6 class="post-author">
                                    <?php echo htmlspecialchars($post["firstname"]) . " " . htmlspecialchars($post["lastname"]); ?>
                                </h6>
                                <h6 class="post-date">
                                    <?php echo date("F j, Y, g:i a", strtotime($post["dateTime"])); ?>
                                </h6>
                                <p class="card-text"><?php echo htmlspecialchars($post["content"]); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class='text-center'>No posts available.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        // Show notification if it exists
        <?php if ($message): ?>
            document.getElementById('notification').style.display = 'block';
            setTimeout(function() {
                document.getElementById('notification').style.display = 'none';
            }, 5000);
        <?php endif; ?>
    </script>
</body>

</html>
