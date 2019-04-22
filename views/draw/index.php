<html>
<head>
    <title>
        Матричная рисовалка
    </title>
    <link rel="stylesheet" href="/css/site.css" >
    <link rel="stylesheet" href="/css/board.css" >
</head>
<body>


    <h1></h1>

    <p>
        Здесь будет рисовалка в виде таблицы и интерфейса к ней
    </p>
    <table class="pixelBoard">
        <?php
            for ($row = 1; $row <= 30; $row++) {
                echo '<tr>';
                for ($col = 1 ; $col <=30; $col++) {
                    echo "<td class='pixelCell' data-row='{$row}' data-col='{$col}' style='background: {$pixelColors[$row][$col]}'></td>";
                }
                echo'</tr>';
            }
        ?>

    </table>

    <div id="app">
        <div>
            <pixels></pixels>
        </div>
    </div>

</body>
</html>

<script type="text/javascript" src="app.js"></script>
