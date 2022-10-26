<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
</head>
<body>
    <pre>
        <?php
         $kk = [
             0 => [
                 0=> 'Hello',
             ]
         ];
//         echo $kk[0][0];
        ?>
    </pre>
    <table align="center" border="1">
        <tr align="center">
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile Number</th>
            <th>Key</th>
        </tr>
        <?php
            $students = $data->studentsData();
            foreach ($students as $key => $student){
        ?>
        <tr align="center">
            <td><?php echo $student['id']; ?></td>
            <td><?php echo $student['name']; ?></td>
            <td><?php echo $student['email']; ?></td>
            <td><?php echo $student['mobile']; ?></td>
            <td><?php echo $key; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
