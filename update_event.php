<?php

include_once "./db.php";

$Events->save($_POST);

to("./index.php");

?>