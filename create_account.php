<?php
/**
 * Allow users to create a new user account
 */
include 'utilities.php';

connect_to_id_store();
?>

<html>
<head>
    <title>PHP Reference Gateway</title>

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>


<body>

<nav class="navbar navbar-default navbar-static-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">PHP Reference Gateway</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="disabled"><a href="home.php">Home</a></li>
        <li class="disabled"><a href="create_experiment.php">Create experiment</a></li>
        <li class="disabled"><a href="manage_experiments.php">Manage experiments</a></li>    
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php">Log in</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="container">
    
    <h3>Create a new account</h3>




    <?php

    if (form_submitted())
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($idStore->username_exists($username))
        {
            print_error_message('The username you entered is already in use. Please select another.');
        }
        else
        {
            $idStore->add_user($username,$password);
            print_success_message('New user created!');
            redirect('login.php');
        }
    }

    ?>



 



    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
        <div class="form-group">
            <label class="sr-only" for="username">Username</label>
            <input type="text" class="form-control" name="username" id="username" placeholder="Username">
        </div>
        <div class="form-group">
            <label class="sr-only" for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
        </div>
        <input name="Submit" type="submit" class="btn btn-primary" value="Submit">
    </form>

    <!--<a href="login.php">Go to login</a>-->

</div>
</body>
</html>