<?php
include("connect.php");

// Fetch posts that are not deleted, along with user information
$sql = "
    SELECT p.content, p.dateTime, p.attachment, u.username, ui.firstname, ui.lastname
    FROM posts p
    JOIN users u ON p.userID = u.userID
    JOIN userInfo ui ON u.userInfoID = ui.userInfoID
    WHERE p.isDeleted = 'no'
    ORDER BY p.dateTime DESC
";
$result = $conn->query($sql);
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

    /* Dark mode styles */
    body.dark-mode {
      background-color: #181818;
      color: #e4e4e4;
    }
    .container-fluid.dark-mode {
      background-color: #282828;
    }
    .post-card.dark-mode {
      background-color: #242424;
      box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1);
    }
    .post-date.dark-mode {
      color: #aaa;
    }
    .post-author.dark-mode {
      color: #e4e4e4;
    }
  </style>
</head>

<body>
  <div class="container-fluid shadow mb-5">
    <p class="h1 m-3">Message Mate</p>
    <button id="theme-toggle" class="btn btn-secondary position-absolute" style="top: 15px; right: 15px;">Dark Mode</button>
  </div>

  <div class="container feed-container">
    <div class="row">
      <?php if ($result->num_rows > 0) {
        while ($post = $result->fetch_assoc()) { ?>
          <div class="col-12">
            <div class="post-card">
              <?php if ($post["attachment"]) { ?>
                <img src="uploads/<?php echo htmlspecialchars($post["attachment"]); ?>" alt="Post Image">
              <?php } ?>
              <div class="post-content">
                <h6 class="post-author"><?php echo htmlspecialchars($post["firstname"]) . " " . htmlspecialchars($post["lastname"]); ?></h6>
                <h6 class="post-date"><?php echo date("F j, Y, g:i a", strtotime($post["dateTime"])); ?></h6>
                <p class="card-text"><?php echo htmlspecialchars($post["content"]); ?></p>
              </div>
            </div>
          </div>
      <?php }
      } else {
        echo "<p class='text-center'>No posts available.</p>";
      } ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    const themeToggleButton = document.getElementById("theme-toggle");
    const body = document.body;
    const containerFluid = document.querySelector(".container-fluid");
    const postCards = document.querySelectorAll(".post-card");

    themeToggleButton.addEventListener("click", () => {
      body.classList.toggle("dark-mode");
      containerFluid.classList.toggle("dark-mode");
      postCards.forEach(card => card.classList.toggle("dark-mode"));
      themeToggleButton.textContent = body.classList.contains("dark-mode") ? "Light Mode" : "Dark Mode";
    });
  </script>
</body>

</html>
