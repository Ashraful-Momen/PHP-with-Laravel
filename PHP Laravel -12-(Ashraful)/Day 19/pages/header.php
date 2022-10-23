<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML and PHP</title>
</head>
<body>
    <table align="center"border="1px solid black">
        <tr align="center">
            <td width="100px">Name</td>
            <td width="100px">ID</td>
            <td width="100px">Dept</td>
            <td width="100px">Year</td>
        </tr>
            <?php
                $person = $data->dataprint();
                foreach($person as $persons){
                ?>
                <tr align="center">
                    <td width="100px"><?php echo $persons['Name'] ?></td>
                    <td width="100px"><?php echo $persons['ID'] ?></td>
                    <td width="100px"><?php echo $persons['Dept'] ?></td>
                    <td width="100px"><?php echo $persons['Year'] ?></td>
                </tr>
                <?php 
                }
            ?>
    </table>
</body>
</html>