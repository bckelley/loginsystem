<?PHP
    include '../includes/server.php';
    if (!isAdmin()) {
        $_SESSION['msg'] = "You must be an Admin.";
        header('location: ../index.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Registration system PHP and MySQL - Create user</title>
	<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
	<style>
		.header {
            width: 70%;
			background: #003366;
		}
        .content {
            width: 70%;
            margin: 0px auto;
            padding: 20px;
            border: 1px solid #B0C4DE;
            background: white;
            border-radius: 0px 0px 10px 10px;
        }
		button[name=register_btn] {
			background: #003366;
		}
	</style>
</head>
<body>
	<div class="header">
		<h2>Admin Panel</h2>
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
                            <a href="../">Back</a> | 
                            <a href="./create_user.php">Create User</a> |
                        <?PHP endif; ?>
                        <a href="index.php?logout='1'" style="color:#f00">Logout</a>
                    </small>
                <?PHP endif; ?>
            </div>
        </div>

        <!-- insert table of users here -->

    </div>
	
</body>
</html>