<?php 

    $fileWithName = 'files/names.txt';
    $registrations = 'files/registrations.txt';

    if (!file_exists('files')) {
        mkdir('files', 0755);
    }

    function isNameCorrect ($name) {

        $expldeName = explode (" ", $name);

        return count($expldeName) > 1 ? true : false;
    }

    function isUserInRegister ($name) {
        global $fileWithName;

        if (file_exists($fileWithName)) {
            $names = file_get_contents($fileWithName);

            $explodeName = explode("\n", $names);

            if (in_array($name, $explodeName)) {
                return true;
            }
        }

        return false;
    }

    function save($name, $select, $problem) {
        global $registrations;

        file_put_contents($registrations, date("Y-m-d H:i:s") . "|" . $name . "|" . $select . "|" . $problem . "\n", FILE_APPEND);
    }

    function getRegistrations () {
        global $registrations;

        $registerUsers = [];

        if (file_exists($registrations)) {

            $data = explode("\n", trim(file_get_contents($registrations), "\n"));

            $registerUsers = [];

            if (!empty($data)) : foreach ($data as $row) {
                $explodeRow = explode ("|", $row);

                $registerUsers[] = [
                    'date' => isset($explodeRow[0]) ? $explodeRow[0] : "",
                    'name' => isset($explodeRow[1]) ? $explodeRow[1] : "",
                    'select' => isset($explodeRow[2]) ? $explodeRow[2] : "",
                    'problem' => isset($explodeRow[3]) ? $explodeRow[3] : "",
                ];
            } endif;
        }

        return $registerUsers;
    }

    $name = strtolower(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $select = filter_input(INPUT_POST, 'select', FILTER_SANITIZE_STRING);
    $problem = filter_input(INPUT_POST, 'problem', FILTER_SANITIZE_STRING);

    $btn = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if (isset($btn) && $btn == 'register') {

        $error = [];
        $success = [];

        if (!isNameCorrect ($name)) {
            $error['name'] = 'Name is problematic';
        }

        if (empty($select)) {
            $error['select'] = 'Select cannot be empty';
        } elseif ($select == 'no_reg') {
            if (empty($problem)) {
                $error['problem'] = 'Problem cannot be empty';
            }
        }

        if (empty($error)) {
            //1. kontroll nimele
            //1.1 ei ole -> okei
            //1.2 kui on, siis veateade

            if (isUserInRegister($name)) {
                $error['name'] = "User is already registered"; 
            }

            if (empty($error)) {
                file_put_contents($fileWithName, $name . "\n", FILE_APPEND);

                save($name, $select, $problem);

                $success['name'] = "User registered";
            }
        }
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Registration</title>
  </head>
  <body>
    <div class="container">

        <h1>Registration</h1>

        <?php echo empty($error) ? "" : '<div class="alert alert-danger"><ul><li>' . join("</li><li>", $error) . '</li></ul></div>'; ?>
        <?php echo empty($success) ? "" : '<div class="alert alert-success"><ul><li>' . join("</li><li>", $success) . '</li></ul></div>'; ?>

        <form method="POST">
            <input name="name" placeholder="Add first and last name" class="form-control"><br>
            
            <input type="radio" name="select" value="reg"> Register
            <input type="radio" name="select" value="no_reg"> No Register<br><br>

            <textarea name="problem" placeholder="Add problem" class="form-control"></textarea><br>

            <button type="submit" name="action" value="register" class="btn btn-success">Register</button>
        </form>

        <?php $getRegistrations = getRegistrations(); 
        if (!empty($getRegistrations)) : ?>
            <hr>
            <table class="table">
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Select</th>
                    <th>Problem</th>
                </tr>
                <?php foreach ($getRegistrations as $user) : extract($user); ?>
                    <tr>
                        <td><?php echo $date ?></td>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $select; ?></td>
                        <td><?php echo $problem; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>