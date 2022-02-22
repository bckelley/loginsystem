<?php 
    include 'includes/server.php';
    // session_start(); 

    if (!isLoggedIn()) {
        $_SESSION['msg'] = "You must be logged in first.";
        header('location: login.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>
<body>

<div class="header">
	<h2>Home Page</h2>
</div>
<div class="content">
  	<!-- notification message -->
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php 
          	echo $_SESSION['success']; 
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>

    <div class="profile_info">
        <img src="images/" />

        <div>
            <?PHP if(isset($_SESSION['user'])): ?>
                <strong><?PHP echo $_SESSION['user']['username'] ?></strong>

                <small>
                    <i style="color:#888">(<?PHP echo $_SESSION['user']['role']; ?>)</i>
                    <br />
                    <?PHP if ($_SESSION['user']['role'] == 'admin'): ?>
                        <a href="admin/">Admin</a> | 
                    <?PHP endif; ?>
                    <a href="index.php?logout='1'" style="color:#f00">Logout</a>
                </small>
            <?PHP endif; ?>
        </div>
    </div>
</div>
		
</body>
</html>