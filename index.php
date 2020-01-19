<?php

require_once('helpers.php');

$mysqli = new mysqli('5.153.13.148', 'kfkfk_user_test', 'LKo7Xk5JdY8icAeH', 'kfkfk_test_db');

if (mysqli_connect_errno()) {
  echo 'Connection failed.<br>' . mysqli_connect_error();
  die;
}

$query = "SELECT id,email,phone,ip FROM users";

$errors = [
  'email' => '',
  'phone' => '',
  'ip_address' => '',
];


// Form Validations
if (isset($_POST['submit'])) {

  $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
  $email = mysqli_real_escape_string($mysqli, $email);
  $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
  $phone = mysqli_real_escape_string($mysqli, $phone);
  $ip_address = trim(filter_input(INPUT_POST, 'ip_address', FILTER_SANITIZE_STRING));
  $ip_address = mysqli_real_escape_string($mysqli, $ip_address);
  $phoneRegExp = "/^0[2-9]\d{7,8}$/";
  $ipRegExp = "/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
  $form_valid = true;

  if (!$email || mb_strlen($email) > 100) {
    $errors['email'] = 'A valid Email is required';
    $form_valid = false;
  }

  if (!preg_match($phoneRegExp, $phone)) {
    $errors['phone'] = 'A valid Phone Number is required';
    $form_valid = false;
  }

  if (!preg_match_all($ipRegExp, $ip_address)) {
    $errors['ip_address'] = 'A valid IP Address is required';
    $form_valid = false;
  }

  if ($form_valid) {
    $token =  md5(rand(1, 10000) . 'mobile' . rand(1, 1000) . 'brain');
    $query = "INSERT INTO users VALUES (null, '$email', '$phone', '$token', '$ip_address')";
    $result = $mysqli->query($query);

    if ($result && $mysqli->affected_rows > 0) {
      header('location: ./');
      exit;
    }
  }
}


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
  <link rel="stylesheet" href="css/style.css">
  <title>Mobile-Brain | Test</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row mb-5">
      <div class="col-xl-3 col-md-8 mx-auto text-center p-3">
        <div class="col-12 border rounded p-3 sticky-top">
          <h3 class="mb-4">Add a New User</h3>
          <form action="" method="POST" novalidate="novalidate" autocomplete="off">
            <div class="form-group">
              <div class="d-flex align-items-center">
                <i class="fas fa-envelope mr-2 fa-lg text-primary"></i>
                <input type="email" name="email" id="email" value="<?= old('email') ?>" class="form-control" placeholder="Email">
              </div>
              <span class="text-danger"><?= $errors['email'] ?></span>
            </div>
            <div class="form-group my-3">
              <div class="d-flex align-items-center">
                <i class="fas fa-phone mr-2 fa-lg text-primary"></i>
                <input type="tel" name="phone" id="phone" value="<?= old('phone') ?>" class="form-control" placeholder="Phone">
              </div>
              <span class="text-danger"><?= $errors['phone'] ?></span>
            </div>
            <div class="form-group">
              <div class="d-flex align-items-center">
                <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                <input type="text" name="ip_address" id="ip_address" value="<?= old('ip_address') ?>" class="form-control" placeholder="IP Address">
              </div>
              <span class="text-danger"><?= $errors['ip_address'] ?></span>
            </div>
            <div class="form-group">
              <input type="submit" name="submit" value="Add User" class="btn btn-outline-primary rounded-pill mt-2">
            </div>
          </form>
        </div>
      </div>
      <div class="col-xl-9 col-md-12 mt-5 text-center">
        <h3>Users Table</h3>
        <table class="table table-striped mt-5">
          <thead>
            <tr>
              <th>#</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Location</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result = $mysqli->query($query)) : ?>
              <?php while ($user = $result->fetch_assoc()) : ?>
                <?php
                    $ch = curl_init('appslabs.net/mobile-brain-test/cudade.php');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, 'ip=' . $user['ip']);
                    $json = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($json, true);
                    ?>
                <tr>
                  <td><?= $user['id'] ?></td>
                  <td><?= $user['email'] ?></td>
                  <td><?= $user['phone'] ?></td>
                  <td>
                    <img class="mr-2" src="http://appslabs.net/mobile-brain-test/images/flags/<?= $response['countryCode'] ?>.gif">
                    <?= (empty($response['theCity'])) ? $response['theCountry'] : $response['theCountry'] . ', ' . $response['theCity'] ?></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</body>

</html>