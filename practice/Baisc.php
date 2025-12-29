<?php
file_put_contents("proof.txt", "Started at ".date("H:i:s").PHP_EOL, FILE_APPEND);

for($i=0;$i<100;$i++){
    file_put_contents("proof.txt", "helo\n", FILE_APPEND);
}

file_put_contents("proof.txt", "Finished\n\n", FILE_APPEND);
?>