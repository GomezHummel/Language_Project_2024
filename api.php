<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Country Generator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Random Country Generator</h1>
        <div class="results-container">
            <table>
            <?php
            // funct to get random country data
            function getRandomCountry() {
                $url = "https://restcountries.com/v3.1/all";
                $response = file_get_contents($url);
                $countries = json_decode($response, true);
                $randomIndex = array_rand($countries);
                return $countries[$randomIndex];
            }

            // get random country
            $randomCountry = getRandomCountry();
            
            // display random country
            $importantAttributes = ['name', 'region', 'subregion', 'languages', 'population'];
            foreach ($importantAttributes as $attribute) {
                if (isset($randomCountry[$attribute])) {
                    echo "<tr>";
                    echo "<td class='attribute'>" . ucfirst($attribute) . "</td>";
                    echo "<td>";
                    if ($attribute === 'name') {
                        // handle name
                        if (is_array($randomCountry[$attribute])) {
                            echo $randomCountry[$attribute]['common'];
                        } else {
                            echo $randomCountry[$attribute];
                        }
                    } elseif (is_array($randomCountry[$attribute])) {
                        // handle array
                        echo implode(', ', $randomCountry[$attribute]);
                    } elseif ($attribute === 'population') {
                        // format population number
                        echo number_format($randomCountry[$attribute]);
                    } else {
                        echo $randomCountry[$attribute];
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
        </div>
        <div class="button-container">
            <a href="index.php" class="button">Return Home</a>
            <button class="button" onclick="window.location.reload()">Get Another Random Country</button>
        </div>
    </div>
</body>
</html>
