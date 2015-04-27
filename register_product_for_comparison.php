<?php
session_start();
if ($_POST['action']=='add'){
    if (isset($_SESSION['compare_models'])){
        if (strpos($_SESSION['compare_models'], $_POST['id'] . '|')===false){
            $_SESSION['compare_models'] .= $_POST['id'] . '|';
        }
    } else {
        $_SESSION['compare_models'] = $_POST['id'] . '|';
    }
    echo 'added';
} elseif ($_POST['action']=='remove'){
    if (isset($_SESSION['compare_models'])){
        $_SESSION['compare_models'] = str_replace($_POST['id'] . '|', '', $_SESSION['compare_models']);
    }
    echo 'removed';
}
?>