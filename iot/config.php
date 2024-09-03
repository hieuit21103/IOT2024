<?php
require "connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $preset = $_POST['preset'];
    $id = $_POST['id']; 
    $name = $_POST['name'];
    $speed = $_POST['speed'];
    $time = $_POST['time'];

    if ($preset == "Create New") {
        $stmt = $pdo->prepare("INSERT INTO `auto_conf` (`name`, `speed`, `time`, `status`) VALUES (:name, :speed, :time, 1)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':speed', $speed);
        $stmt->bindParam(':time', $time);
        
        if ($stmt->execute()) {
            echo "New configuration created successfully.";
        } else {
            echo "Failed to create new configuration.";
        }
    } else {
        $stmt = $pdo->prepare("UPDATE `auto_conf` SET `status` = 0 WHERE 1");
        $stmt->execute();
        $stmt = $pdo->prepare("UPDATE `auto_conf` SET `name` = :name, `speed` = :speed, `time` = :time, `status` = 1 WHERE `id` = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':speed', $speed);
        $stmt->bindParam(':time', $time);
        
        if ($stmt->execute()) {
            echo "Configuration updated successfully.";
        } else {
            echo "Failed to update configuration.";
        }
    }
}
?>
