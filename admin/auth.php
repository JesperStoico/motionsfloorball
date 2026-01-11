<?php
session_start();

if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}
