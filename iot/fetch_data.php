<?php
require "connect.php";
if (isset($_GET['preset'])) {
    $preset = $_GET['preset'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM auto_conf WHERE id = :preset");
        $stmt->bindParam(':preset', $preset);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Missing parameter']);
}
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM auto_conf WHERE status = :status");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Missing parameter']);
}
?>
