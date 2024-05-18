<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrix Oyunu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: auto;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 10px;
            cursor: pointer;
        }

        th,
        td {
            text-align: center;
        }

        .selected {
            background-color: #ffffff !important;
            /* Grey background for selected cells */
            pointer-events: none;
            /* Disable further clicks on the selected cell */
        }

        .aiselected {
            background-color: #ffffff !important;
            /* Grey background for selected cells */
            pointer-events: none;
            /* Disable further clicks on the selected cell */
        }

        .enabled {
            border: 10px solid black;
            font-weight: bold;
        }

        .red {
            background-color: #ff0000;
            /* Red background */
            color: #ffffff;
            /* White text on red background */
        }

        .blue {
            background-color: #0000ff;
            /* Blue background */
            color: #ffffff;
            /* White text on blue background */
        }

        .cell {
            width: 100px;
            border-radius: 1.25rem;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <h1>Matrix Oyunu</h1>
        <hr>
    </div>
    <div id="startGame" style="text-align: center;">
        <h2>Rengi Seç</h2>
        <label for="renk_kirmizi">Kırmızı</label>
        <input type="radio" id="renk_kirmizi" name="renk" value="red">

        <label for="renk_mavi">Mavi</label>
        <input type="radio" id="renk_mavi" name="renk" value="blue">

        <h2>İlk/Son Seçeneği</h2>
        <label for="secenek_ilk">İlk</label>
        <input type="radio" id="secenek_ilk" name="secenek" value="first">

        <label for="secenek_son">Son</label>
        <input type="radio" id="secenek_son" name="secenek" value="second">
        <br> <br>
        <button class="btn btn-success" onclick="startGame()">Oyunu Başlat</button>
    </div>
    <?php
    function generateRandomMatrix()
    {
        $numbers = range(1, 100);
        shuffle($numbers);

        $matrix = [];
        $redTotal = 0;
        $blueTotal = 0;

        for ($i = 0; $i < 10; $i++) {
            $row = [];
            $redCount = 5;
            $blueCount = 5;

            // Kırmızı ve mavi sayıları seç
            $redNumbers = array_slice($numbers, $i * 10, $redCount);
            $blueNumbers = array_slice($numbers, $i * 10 + $redCount, $blueCount);

            // Kırmızı ve mavi sayıları birleştir ve karıştır
            $mixedNumbers = array_merge($redNumbers, $blueNumbers);
            shuffle($mixedNumbers);

            for ($j = 0; $j < 10; $j++) {
                // Hücrenin rengini belirle
                $colorClass = in_array($mixedNumbers[$j], $redNumbers) ? 'red' : 'blue';
                $row[] = [
                    'value' => $mixedNumbers[$j],
                    'color' => $colorClass,
                ];

                // Hesaplanan toplamları güncelle
                if ($colorClass == 'red') {
                    $redTotal += $mixedNumbers[$j];
                } elseif ($colorClass == 'blue') {
                    $blueTotal += $mixedNumbers[$j];
                }
            }

            $matrix[] = $row;
        }

        return [
            'matrix' => $matrix,
            'redTotal' => $redTotal,
            'blueTotal' => $blueTotal,
        ];
    }

    function checkEqualTotals($matrix)
    {
        return $matrix['redTotal'] == $matrix['blueTotal'];
    }

    do {
        $gameMatrix = generateRandomMatrix();
    } while (!checkEqualTotals($gameMatrix));

    echo '<table id="gameTable" class="d-none">';
    echo '<tbody>';
    foreach ($gameMatrix['matrix'] as $rowIndex => $row) {
        $rowNumber = $rowIndex + 1;
        echo '<tr class="d-flex" data-row="' . $rowNumber . '">';
        foreach ($row as $colIndex => $cell) {
            $colNumber = $colIndex + 1;
            echo '<td data-col="' . $colNumber . '" data-value="' . $cell['value'] . '" class="' . $cell['color'] . ' m-1 cell">' . $cell['value'] . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</br>'
    ?>
    <div id="gameScore" class="container mt-5 d-none">
        <div class="row justify-content-center">
            <div class="col-6">

                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h2 class="card-title">Oyun Durumu: <span id="gameStatus">XXX</span></h2>
                        <hr><br>
                        <div class="row">
                            <div class="col-6">
                                <p id="userText" class="text-center">User Score</p>
                                <hr>
                                <p id="userScoreText" class="text-center">XXX</p>
                            </div>
                            <div class="col-6">
                                <p id="aiText" class="text-center">AI Score</p>
                                <hr>
                                <p id="aiScoreText" class="text-center">XXX</p>
                            </div>
                        </div>
                        <a id="restartGame" href="#" onclick="location.reload()" class="d-none mt-3 btn btn-primary mx-auto">Tekrar Başla</a>
                    </div>
                </div>

            </div>
        </div>
    </div>


</body>


<script>
    var tdElements = document.querySelectorAll('td');
    let userscore = 0;
    let aiscore = 0;
    let tds = null
    let first = true;

    let usercolor = "red"
    let aicolor = "blue"
    let turn = "first"

    function startGame() {
        usercolor = document.querySelector('input[name="renk"]:checked').value;
        aicolor = usercolor == "red" ? "blue" : "red";
        turn = document.querySelector('input[name="secenek"]:checked').value;
        document.getElementById('gameTable').classList.remove('d-none');
        document.getElementById('gameScore').classList.remove('d-none');
        document.getElementById('startGame').classList.add('d-none');
        document.getElementById('userText').style.color = usercolor;
        document.getElementById('aiText').style.color = aicolor;
        document.getElementById('gameStatus').innerHTML = "Devam Ediyor...";
        if (turn == "second") {
            if (aicolor == "red") {
                var selectedCol = maxred(1);
            } else if (aicolor == "blue") {
                var selectedCol = maxblue(1);
            }

            if (selectedCol && !selectedCol.classList.contains('selected') && !selectedCol.classList.contains('aiselected')) {
                aiscore += parseInt(selectedCol.getAttribute('data-value'));
                selectedCol.classList.add('aiselected');
                tds = selectedCol.parentElement.querySelectorAll('td');

                tds.forEach(function(td) {
                    td.classList.add('enabled');
                });
            }
            document.getElementById('userScoreText').innerHTML = userscore;
            document.getElementById('aiScoreText').innerHTML = aiscore;
        }
    }

    function maxblue(colNumber) {
        var selectedColBlueElements = document.querySelectorAll('td[data-col="' + colNumber + '"].blue:not(.selected):not(.aiselected)');
        var maxElement = null;
        var maxValue = -Infinity;
        selectedColBlueElements.forEach(function(element) {
            var value = parseInt(element.getAttribute('data-value'));
            if (value > maxValue) {
                maxValue = value;
                maxElement = element;
            }
        });
        return maxElement;
    }

    function minred(colNumber) {
        var selectedColRedElements = document.querySelectorAll('td[data-col="' + colNumber + '"].red:not(.selected):not(.aiselected)');
        var minElement = null;
        var minValue = +Infinity;
        selectedColRedElements.forEach(function(element) {
            var value = parseInt(element.getAttribute('data-value'));
            if (value < minValue) {
                minValue = value;
                minElement = element;
            }
        });
        return minElement;
    }

    function minblue(colNumber) {
        var selectedColBlueElements = document.querySelectorAll('td[data-col="' + colNumber + '"].blue:not(.selected):not(.aiselected)');
        var minElement = null;
        var minValue = +Infinity;
        selectedColBlueElements.forEach(function(element) {
            var value = parseInt(element.getAttribute('data-value'));
            if (value < minValue) {
                minValue = value;
                minElement = element;
            }
        });
        return minElement;
    }

    function maxred(colNumber) {
        var selectedColRedElements = document.querySelectorAll('td[data-col="' + colNumber + '"].red:not(.selected):not(.aiselected)');
        var minElement = null;
        var minValue = -Infinity;
        selectedColRedElements.forEach(function(element) {
            var value = parseInt(element.getAttribute('data-value'));
            if (value > minValue) {
                minValue = value;
                minElement = element;
            }
        });
        return minElement;
    }

    tdElements.forEach(function(tdElement) {
        tdElement.addEventListener('click', function() {

            var row = this.parentElement.getAttribute('data-row');
            var col = this.getAttribute('data-col');
            var val = this.getAttribute('data-value');
            var color = this.classList[0];

            if (this.classList.contains('enabled') || first == true) {
                if (color == usercolor) {
                    userscore += parseInt(val);
                    this.classList.add('selected');
                } else {
                    userscore -= parseInt(val);
                    this.classList.add('selected');
                }
                if (first == false || turn == "second") {
                    tds.forEach(function(td) {
                        td.classList.remove('enabled');
                    });
                }
                first = false;
            } else {
                return;
            }

            if (aicolor == "red") {
                var selectedCol = maxred(col);
                if (selectedCol == null) {
                    var selectedBlue = minblue(col);
                }
            } else if (aicolor == "blue") {
                var selectedCol = maxblue(col);
                if (selectedCol == null) {
                    var selectedRed = minred(col);
                }
            }

            if (selectedCol && !selectedCol.classList.contains('selected') && !selectedCol.classList.contains('aiselected')) {
                aiscore += parseInt(selectedCol.getAttribute('data-value'));
                selectedCol.classList.add('aiselected');
                tds = selectedCol.parentElement.querySelectorAll('td');

                tds.forEach(function(td) {
                    td.classList.add('enabled');
                });
            }

            if (selectedBlue && !selectedBlue.classList.contains('selected') && !selectedBlue.classList.contains('aiselected')) {
                aiscore -= parseInt(selectedBlue.getAttribute('data-value'));
                selectedBlue.classList.add('aiselected');
                tds = selectedBlue.parentElement.querySelectorAll('td');

                tds.forEach(function(td) {
                    td.classList.add('enabled');
                });
            }

            if (selectedRed && !selectedRed.classList.contains('selected') && !selectedRed.classList.contains('aiselected')) {
                aiscore -= parseInt(selectedRed.getAttribute('data-value'));
                selectedRed.classList.add('aiselected');
                tds = selectedRed.parentElement.querySelectorAll('td');

                tds.forEach(function(td) {
                    td.classList.add('enabled');
                });
            }

            var hasMove = document.querySelectorAll('td.enabled:not(.selected):not(.aiselected)').length > 0;

            if (!hasMove) {
                // console.error("Oyun Bitti User");
                // console.log("UserScore: " + userscore);
                // console.log("AIScore: " + aiscore);
                if (userscore > aiscore) {
                    document.getElementById('gameStatus').innerHTML = "Kazanan User";
                } else if (userscore < aiscore) {
                    document.getElementById('gameStatus').innerHTML = "Kazanan AI";
                } else if (userscore == aiscore) {
                    document.getElementById('userScoreText').innerHTML = userscore;
                    document.getElementById('aiScoreText').innerHTML = aiscore;
                    document.getElementById('gameStatus').innerHTML = "Berabere";
                }
                document.getElementById('restartGame').classList.remove('d-none');
                return;
            }

            if (selectedCol == null && selectedRed == null && selectedBlue == null) {
                // console.error("Oyun Bitti AI");
                // console.log("UserScore: " + userscore);
                // console.log("AIScore: " + aiscore);
                if (userscore > aiscore) {
                    document.getElementById('gameStatus').innerHTML = "Kazanan User";
                } else if (userscore < aiscore) {
                    document.getElementById('gameStatus').innerHTML = "Kazanan AI";
                } else if (userscore == aiscore) {
                    document.getElementById('gameStatus').innerHTML = "Berabere";
                    document.getElementById('userScoreText').innerHTML = userscore;
                    document.getElementById('aiScoreText').innerHTML = aiscore;
                }
                document.getElementById('restartGame').classList.remove('d-none');
                return;
            }

            document.getElementById('userScoreText').innerHTML = userscore;
            document.getElementById('aiScoreText').innerHTML = aiscore;

            // Konsol Loglama
            // console.log(selectedCol);
            // console.log(selectedRed);
            // console.log("--------------------");
            // console.log("UserScore: " + userscore);
            // console.log("--------------------");
            // console.log("AIScore: " + aiscore);
            // this.classList.add('selected');


        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</html>