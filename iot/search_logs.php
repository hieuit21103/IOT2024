<?php
require "connect.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = isset($_GET['query']) ? $_GET['query'] : '';
$searchTerm = '%' . $query . '%';

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM logs WHERE mode LIKE :query OR station LIKE :query OR speed LIKE :query");
    $stmt->bindParam(':query', $searchTerm);
    $stmt->execute();
    $totalRows = $stmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    $stmt = $pdo->prepare("SELECT * FROM logs WHERE mode LIKE :query OR station LIKE :query OR speed LIKE :query LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':query', $searchTerm);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'logs' => $logs,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>
