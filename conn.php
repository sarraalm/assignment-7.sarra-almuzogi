<?php 
    $hn = 'localhost';
    $db = 'expense tracker';
    $un = 'root';
    $pw = '';
    
    $con =  mysqli_connect($hn, $un, $pw, $db);

    if($con)
    {
      //echo"database is connect";
    }     
    else{

      echo"database is not connect";
    }
?>