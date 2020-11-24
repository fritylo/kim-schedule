<?php

$db = new mysqli('localhost', 'phpmyadmin', 'Jyie-ha0r-kgie', 'fti-schedule');

function query(string $sql) : array {
   return task($sql)->fetch_all(MYSQLI_ASSOC);
}

function task(string $sql) : mysqli_result {
   global $db;
   return $db->query($sql);
}