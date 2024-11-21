<?php
include("connect.php");

// Initialize message variable
$message = "";

// POST SUBMISSION
if (isset($_POST['btnSubmitPost'])) {
    $content = $_POST['content'];
    $attachment = $_FILES['attachment']['name'];

    // Move uploaded file to the 'uploads' directory if an attachment exists
    if ($attachment) {
        move_uploaded_file($_FILES['attachment']['tmp_name'], "uploads/" . $attachment);
    }

    // Insert post
    $postQuery = "INSERT INTO posts (content, dateTime, attachment, userID, isDeleted) 
                  VALUES ('$content', NOW(), '$attachment', '6', 'no')";

    if (mysqli_query($conn, $postQuery)) {
        $message = "Successfully posted!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// DELETE POST
if (isset($_POST["postID"])) {
    $postID = $_POST["postID"];
    $deleteSQL = "UPDATE posts SET isDeleted = 'yes' WHERE postID = '$postID'";
    if (mysqli_query($conn, $deleteSQL)) {
        header("Location: " . $_SERVER['PHP_SELF']); // Refresh page
        exit;
    } else {
        $message = "Error deleting post: " . mysqli_error($conn);
    }
}

// EDIT POST
if (isset($_POST['btnEditPost'])) {
    $postID = $_POST['editPostID'];
    $updatedContent = $_POST['editContent'];

    $updateSQL = "UPDATE posts SET content = '$updatedContent' WHERE postID = '$postID'";
    if (mysqli_query($conn, $updateSQL)) {
        $message = "Post updated successfully!";
    } else {
        $message = "Error updating post: " . mysqli_error($conn);
    }
}

// GET POSTS
$sql = "
    SELECT p.postID, p.content, p.dateTime, p.attachment, ui.firstname, ui.lastname
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Transforma';
            src: url('transforma-font/transforma-font.otf') format('opentype');
        }

        body {
            background-color: #f0f2f5;
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
            padding: 15px;
        }

        .post-card img {
            max-width: 100%;
            border-radius: 8px;
        }

        .post-author {
            font-weight: bold;
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

        /* Consistent size for buttons */
        .post-btn,
        .edit-btn,
        .delete-btn {
            font-size: 0.875rem;
            /* Slightly smaller font size */
            padding: 6px 12px;
            /* Consistent padding */
            width: 120px;
            /* Consistent width */
            text-align: center;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .post-btn {
            background-color: #007bff;
            color: white;
        }

        .post-btn:hover {
            background-color: #0056b3;
        }

        .edit-btn {
            background-color: #ffc107;
            color: white;
        }

        .edit-btn:hover {
            background-color: #e0a800;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container-fluid shadow mb-5 text-center p-3 bg-white">
        <p class="h1">Message Mate</p>
    </div>

    <!-- Notification -->
    <?php if ($message): ?>
        <div class="notification" id="notification">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Post Form -->
    <div class="container feed-container">
        <form class="mb-4 p-3 bg-light rounded shadow" method="POST" enctype="multipart/form-data">
            <textarea class="form-control mb-3" name="content" placeholder="What's on your mind?" required></textarea>
            <input class="form-control mb-3" type="file" name="attachment" accept="image/*">
            <button type="submit" name="btnSubmitPost" class="btn post-btn">Post</button>
        </form>
    </div>

    <!-- Posts -->
    <div class="container feed-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($post = mysqli_fetch_assoc($result)): ?>
                <div class="post-card">
                    <?php if ($post["attachment"]): ?>
                        <img src="uploads/<?php echo htmlspecialchars($post["attachment"]); ?>" alt="Post Image">
                    <?php endif; ?>
                    <h6 class="post-author">
                        <?php echo htmlspecialchars($post["firstname"]) . " " . htmlspecialchars($post["lastname"]); ?>
                    </h6>
                    <small><?php echo date("F j, Y, g:i a", strtotime($post["dateTime"])); ?></small>
                    <p id="content-display-<?php echo $post['postID']; ?>">
                        <?php echo htmlspecialchars($post["content"]); ?>
                    </p>
                    <form id="edit-form-<?php echo $post['postID']; ?>" class="d-none" method="post">
                        <textarea name="editContent"
                            class="form-control mb-2"><?php echo htmlspecialchars($post['content']); ?></textarea>
                        <input type="hidden" name="editPostID" value="<?php echo htmlspecialchars($post['postID']); ?>">
                        <button type="submit" name="btnEditPost" class="btn btn-success btn-sm">Save</button>
                        <button type="button" onclick="cancelEdit('<?php echo $post['postID']; ?>')"
                            class="btn btn-secondary btn-sm">Cancel</button>
                    </form>
                    <!-- Flex container for independent buttons -->
                    <div class="d-flex gap-2">
                        <button class="btn edit-btn" onclick="enableEdit('<?php echo $post['postID']; ?>')">Edit</button>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="postID" value="<?php echo htmlspecialchars($post['postID']); ?>">
                            <button type="submit" class="btn delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No posts available.</p>
        <?php endif; ?>
    </div>


    <script>
        // Show notification if it exists
        <?php if ($message): ?>
            document.getElementById('notification').style.display = 'block';
            setTimeout(function () {
                document.getElementById('notification').style.display = 'none';
            }, 5000);
        <?php endif; ?>

        // Enable edit mode
        function enableEdit(postID) {
            document.getElementById(`content-display-${postID}`).style.display = 'none';
            document.getElementById(`edit-form-${postID}`).classList.remove('d-none');
        }

        // Cancel edit
        function cancelEdit(postID) {
            document.getElementById(`content-display-${postID}`).style.display = 'block';
            document.getElementById(`edit-form-${postID}`).classList.add('d-none');
        }
    </script>
</body>

</html>