<?php
require 'connect.php';

if (isset($_GET['timestamp']) && isset($_GET['mode']) && isset($_GET['station']) && isset($_GET['speed'])) {
    $timestamp = $_GET['timestamp'];
    $mode = $_GET['mode'];
    $station = $_GET['station'];
    $speed = $_GET['speed'];

    $stmt = $pdo->prepare("INSERT INTO logs (timestamp, mode, station, speed) VALUES (:timestamp, :mode, :station, :speed)");
    $stmt->bindParam(':timestamp', $timestamp);
    $stmt->bindParam(':mode', $mode);
    $stmt->bindParam(':station', $station);
    $stmt->bindParam(':speed', $speed);
    if ($stmt->execute()) {
        echo "Log entry added successfully.";
    } else {
        echo "Failed to add log entry.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4"><a href="/iot" style="text-decoration: none;">Log Data</a></h1>
        <div class="mb-4">
            <input type="text" id="search" class="form-control" placeholder="Search logs">
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Timestamp</th>
                    <th>Mode</th>
                    <th>Station</th>
                    <th>Speed</th>
                </tr>
            </thead>
            <tbody id="logTable">
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center" id="pagination">
            </ul>
        </nav>
    </div>
    <script>
        function fetchLogs(page = 1) {
            const query = document.getElementById('search').value;
            axios.get('search_logs.php', {
                params: {
                    query: query,
                    page: page
                }
            })
            .then(function (response) {
                const data = response.data;
                const logs = data.logs;
                const totalPages = data.totalPages;
                const logTable = document.getElementById('logTable');
                const pagination = document.getElementById('pagination');
                logTable.innerHTML = '';
                pagination.innerHTML = '';

                if (logs.length > 0) {
                    logs.forEach(function (log) {
                        const row = `<tr>
                            <td>${log.id}</td>
                            <td>${log.timestamp}</td>
                            <td>${log.mode}</td>
                            <td>${log.station}</td>
                            <td>${log.speed}</td>
                        </tr>`;
                        logTable.innerHTML += row;
                    });
                    for (let i = 1; i <= totalPages; i++) {
                        const pageItem = document.createElement('li');
                        pageItem.className = 'page-item';
                        if (i === page) {
                            pageItem.classList.add('active');
                        }
                        pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                        pagination.appendChild(pageItem);

                        pageItem.addEventListener('click', function (e) {
                            e.preventDefault();
                            fetchLogs(i);
                        });
                    }
                } else {
                    logTable.innerHTML = '<tr><td colspan="5" class="text-center">No logs found</td></tr>';
                }
            })
            .catch(function (error) {
                console.error('There was an error!', error);
            });
        }

        document.getElementById('search').addEventListener('input', function () {
            fetchLogs(1);
        });
        fetchLogs();
    </script>
</body>
</html>
