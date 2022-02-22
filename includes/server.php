<?PHP

    session_start();

    $username   = "";
    $email      = "";
    $errors = array(); 

    $db = mysqli_connect('localhost', 'root', 'admin', 'advloginsystem');

    if (isset($_POST['login_btn'])) {
        login();
    }
    // LOGIN USER
    function login(){
        global $db, $username, $errors;

        // grap form values
        $username = e($_POST['username']);
        $password = e($_POST['password']);

        // make sure form is filled properly
        if (empty($username)) { array_push($errors, "Username is required"); }
        if (empty($password)) { array_push($errors, "Password is required"); }

        // attempt login if no errors on form
        if (count($errors) == 0) {
            $password = md5($password);

            $query = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
            $results = mysqli_query($db, $query);

            if (mysqli_num_rows($results) == 1) { 
                // check if user is admin or user
                $logged_in_user = mysqli_fetch_assoc($results);
                if ($logged_in_user['role'] == 'admin') {

                    $_SESSION['user'] = $logged_in_user;
                    $_SESSION['success']  = "You are now logged in";
                    header('location: index.php');		  
                }else{
                    $_SESSION['user'] = $logged_in_user;
                    $_SESSION['success']  = "You are now logged in";

                    header('location: index.php');
                }
            }else {
                array_push($errors, "Wrong username/password combination");
            }
        }
    }

    if (isset($_POST['register_btn'])) {
        register();
    }
    function register() {

        global $db, $errors, $username, $email;

        $username       = e($_POST['username']);
        $email          = e($_POST['email']);
        $password       = e($_POST['password']);
        $confirm        = e($_POST['confirm']);

        if (empty($username)) { array_push($errors, "Username is required"); }
        if (empty($email)) { array_push($errors, "Email is required"); }
        if (empty($password)) { array_push($errors, "Password is required"); }
        if ($password != $confirm) {
            array_push($errors, "The two passwords do not match");
        }

        if (count($errors) == 0) {
            $password = md5($password);
    
            if (isset($_POST['role'])) {
                $role = e($_POST['role']);
                $query = "INSERT INTO users (username, email, role, password) 
                          VALUES('$username', '$email', '$role', '$password')";
                mysqli_query($db, $query);
                $_SESSION['success']  = "New user successfully created!!";
                header('location: index.php');
            }else{
                $query = "INSERT INTO users (username, email, role, password) 
                          VALUES('$username', '$email', 'user', '$password')";
                mysqli_query($db, $query);
    
                // get id of the created user
                $logged_in_user_id = mysqli_insert_id($db);
    
                $_SESSION['user'] = getUserById($logged_in_user_id); // put logged in user in session
                $_SESSION['success']  = "You are now logged in";
                header('location: index.php');				
            }

        }

    }

    function getUserById($id) {
        global $db;
        $query = "SELECT * FROM users WHERE id=".$id;
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);
        return $user;
    }

    function e($val) {
        global $db;
        return mysqli_real_escape_string($db, trim($val));
    }

    function display_error() {

        global $errors;
        if (count($errors) > 0) {
            echo '<div class="error">';
                foreach ($errors as $error) {
                    echo $error . '<b/>';
                }
            echo '</div>';
        }

    }
   
    function isLoggedIn() {
        if ( isset($_SESSION['user']) ) {
            return true;
        } else {
            return false;
        }
    }

    if (isset($_GET['logout'])) {
        session_start();
        unset($_SESSION['user']);
        header('location: login.php');
    }

    function isAdmin() {
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin' ) {
            return true;
        }else{
            return false;
        }
    }
    
  ?>