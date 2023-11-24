<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2_Web</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="container">
        <h1>PAÍSES DO MUNDO</h1>
        <form method="GET">
            <div class="form-group">
                <label class="sr-only" for="pesquisa"></label>
                <input required type="text" name="pesquisa" class="form-control" id="pesquisa" placeholder="País ou Capital">
                <button type="submit">Pesquisar</button>
            </div>
        </form>
        <form method="GET">
            <label for="per_page">Itens por página:</label>
            <select name="per_page" id="per_page">
                <option value="10">10</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="239">Todos</option>
            </select>
            <button type="submit">Enter</button>
        </form>
    </div>

    <?php
    $servername = "200.236.3.126";
    $username = "root";
    $password = "example";
    $dbname = "world";

    $conn = mysqli_connect($servername, $username, $password, $dbname);


    $results_per_page = isset($_GET['per_page']) ? $_GET['per_page'] : 100;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;

    $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'CountryName';

    if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
        $termo_pesquisa = $_GET['pesquisa'];

        $sql = "SELECT co.Name AS CountryName, ci.Name AS Capital, 
                GROUP_CONCAT(cl.Language SEPARATOR ', ') AS Languages, 
                co.Population
                FROM Country co
                LEFT JOIN City ci ON co.Capital = ci.ID
                LEFT JOIN CountryLanguage cl ON co.Code = cl.CountryCode
                WHERE co.Name LIKE '%$termo_pesquisa%' OR ci.Name LIKE '%$termo_pesquisa%'
                GROUP BY co.Code
                ORDER BY $orderBy
                LIMIT $start_from, $results_per_page";
    } else {
        $sql = "SELECT co.Name AS CountryName, ci.Name AS Capital, 
                GROUP_CONCAT(cl.Language SEPARATOR ', ') AS Languages, 
                co.Population
                FROM Country co
                LEFT JOIN City ci ON co.Capital = ci.ID
                LEFT JOIN CountryLanguage cl ON co.Code = cl.CountryCode
                GROUP BY co.Code
                ORDER BY $orderBy
                LIMIT $start_from, $results_per_page";
    }

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<table><tr>
                <th><a href='?orderby=CountryName&per_page=$results_per_page'>País</a></th>
                <th><a href='?orderby=Capital&per_page=$results_per_page'>Capital</a></th>
                <th><a href='?orderby=Population&per_page=$results_per_page'>População</a></th>
                <th><a href='?orderby=Languages&per_page=$results_per_page'>Língua(s)</a></th>
              </tr>";

        while ($row = mysqli_fetch_array($result)) {
            echo "<tr><td>" . $row["CountryName"] . "</td>" .
                "<td>" . $row["Capital"] . "</td>" .
                "<td>" . $row["Population"] . "</td>" .
                "<td>" . $row["Languages"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum resultado encontrado.";
    }

    $total_pages_sql = "SELECT COUNT(*) AS total FROM Country";
    $result_total = mysqli_query($conn, $total_pages_sql);
    $total_rows = mysqli_fetch_assoc($result_total)['total'];
    $total_pages = ceil($total_rows / $results_per_page);

    
    echo "<div class='pagination'>";
    if ($current_page > 1) {
        echo "<a href='?page=".($current_page - 1)."&orderby=$orderBy&per_page=$results_per_page'>Anterior</a>";
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<a href='?page=".$i."&orderby=$orderBy&per_page=$results_per_page'>$i</a>";
    }
    if ($current_page < $total_pages) {
        echo "<a href='?page=".($current_page + 1)."&orderby=$orderBy&per_page=$results_per_page'>Próxima</a>";
    }
    echo "</div>";

    mysqli_close($conn);
    ?>
</body>
</html>
