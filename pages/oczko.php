<?php
    session_start();

    /* Podstawowy setup */
    if(isset($_POST["rozpocznijGre"])) {
        rozpocznijGre();
    }
    elseif(!isset($_SESSION["graRozpoczeta"]) || isset($_POST["resetujGre"])) {
        $_SESSION["graRozpoczeta"] = false;
    }
    if(!isset($_SESSION["graRozstrzygnieta"]) || isset($_POST["resetujGre"])) {
        $_SESSION["graRozstrzygnieta"] = false;
    }
    if(isset($_SESSION["gracze"])) {
        $krupier = $_SESSION["gracze"][0];
        $gracz = $_SESSION["gracze"][1];
    }

    /* Kontrola gry */
    if($_SESSION["graRozpoczeta"]) {

        /* Obsługa zdarzeń dobrania i pasowania */
        if(isset($_POST["dobierz"])) {
            dobierzKarte($gracz);
        }
        elseif(isset($_POST["pasuj"])) {
            while($_SESSION["sumaKrupiera"] < 16) {
                dobierzKarte($krupier);
            }
            if($_SESSION["sumaKrupiera"] > 21) {
                zakonczGre($gracz, "fura");
            }
            else {
                if($_SESSION["sumaKrupiera"] > $_SESSION["sumaGracza"]) {
                zakonczGre($krupier, "");
                }
                elseif($_SESSION["sumaGracza"] > $_SESSION["sumaKrupiera"]) {
                    zakonczGre($gracz, "");
                }
                else {
                    zakonczGre("remis", "");
                }
            }
        }

        /* Śledzenie wyniku */
        if(isset($_POST["rozpocznijGre"])) {    // Proste sprawdzenie pierwszej "rundy", by wykryć blackjacka (natychmiastowe 21)
            if($_SESSION["sumaKrupiera"] === 22 && $_SESSION["sumaGracza"] === 22) {
                zakonczGre("remis", "perskieOczko");
            }
            elseif($_SESSION["sumaKrupiera"] === 22) {
                zakonczGre($krupier, "perskieOczko");
            } 
            elseif($_SESSION["sumaGracza"] === 22) {
                zakonczGre($gracz, "perskieOczko");
            }
            elseif($_SESSION["sumaKrupiera"] === 21 && $_SESSION["sumaGracza"] === 21) {
                zakonczGre("remis", "blackjack");
            }
            elseif($_SESSION["sumaKrupiera"] === 21) {
                zakonczGre($krupier, "blackjack");
            }
            elseif($_SESSION["sumaGracza"] === 21) {
                zakonczGre($gracz, "blackjack");
            }
            // Nie powinno do tego dojść, ale na wszelki wypadek lepiej zabezpieczyć się przed furą w pierwszej "rundzie"
            elseif($_SESSION["sumaKrupiera"] > 22 && $_SESSION["sumaGracza"] > 22) {
                zakonczGre("remis", "fura");
            }
            elseif($_SESSION["sumaKrupiera"] > 22) {
                zakonczGre($gracz, "fura");
            }
            elseif($_SESSION["sumaGracza"] > 22) {
                zakonczGre($krupier, "fura");
            }
        }
        elseif($_SESSION["sumaGracza"] > 21) {
            zakonczGre($krupier, "fura");
        }
    }

    class Gracz {
        public $nazwa;
        public $karty;
        function __construct($nazwa, $karty) {
            $this->nazwa = $nazwa;
            $this->karty = $karty;
        }
    }
    class Karta {
        public $nazwa;
        public $wartosc;
        public $kolor;
        public $ikona;
        public $zakryta = false;
        function __construct($nazwa, $wartosc, $kolor, $ikona) {
            $this->nazwa = $nazwa;
            $this->wartosc = $wartosc;
            $this->kolor = $kolor;
            $this->ikona = $ikona;
        }
    }

    /* Dodanie karty o wszystkich 4 kolorach */
    function dodajKarteDoTalii(&$talia, $nazwa) {
        $kolory = ["clubs", "diamonds", "hearts", "spades"];
        $folder_z_ikonami = "../resources/blackjack-cards";
        switch($nazwa) {
            case "ace":
                $wartosc = 11;
                break;
            case "king":
                $wartosc = 4;
                break;
            case "queen":
                $wartosc = 3;
                break;
            case "jack":
                $wartosc = 2;
                break;
            default:
                $wartosc = intval($nazwa);
        }
        for($kolor = 0; $kolor < 4; $kolor++) {
            array_push($talia,
                new Karta(
                    $nazwa,
                    $wartosc,
                    $kolory[$kolor],
                    $folder_z_ikonami . "/" . $nazwa . "_of_" . $kolory[$kolor] . ".svg"
                )
            );
        }
    }

    /* Wyświetlenie obrazków wszystkich kart gracza */
    function wyswietlKarty($gracz) {
        for($i = 0; $i < count($gracz->karty); $i++) {
            $karta = $gracz->karty[$i];
            if($karta->zakryta) {
                $nazwaKarty = "???";
                $ikona = dirname($karta->ikona) . "/back.svg";
            }
            else {
                $nazwaKarty = ucfirst($karta->nazwa) . " of " . $karta->kolor;
                $ikona = $karta->ikona;
            }
            echo "<img src = '" . $ikona . "' alt = '" . $nazwaKarty . "' title = '" . $nazwaKarty . "' class = 'karta'>";
        }
    }

    /* Rozpoczęcie gry */
    function rozpocznijGre() {
        // Ustalenie graczy
        $_SESSION["gracze"] = [];
        $krupier = new Gracz("Krupier", []);
        $gracz = new Gracz("Gracz", []);
    
        // Wypełnienie talii
        $talia = [];
        for($wartosc = 2; $wartosc <= 10; $wartosc++) {
            dodajKarteDoTalii($talia, "$wartosc");
        }
        dodajKarteDoTalii($talia, "ace");
        dodajKarteDoTalii($talia, "king");
        dodajKarteDoTalii($talia, "queen");
        dodajKarteDoTalii($talia, "jack");
    
        // Tasowanie
        shuffle($talia);
    
        // Rozdanie pierwszych kart
        array_push($krupier->karty, array_pop($talia), array_pop($talia));
        $krupier->karty[1]->zakryta = true;
        array_push($gracz->karty, array_pop($talia), array_pop($talia));
        
        // Ustawienie zmiennych sesji
        array_push($_SESSION["gracze"], $krupier, $gracz);
        $_SESSION["talia"] = $talia;
        $_SESSION["graRozpoczeta"] = true;
        $_SESSION["sumaKrupiera"] = $krupier->karty[0]->wartosc + $krupier->karty[1]->wartosc;
        $_SESSION["sumaGracza"] = $gracz->karty[0]->wartosc + $gracz->karty[1]->wartosc;
    }

    /* Dobranie karty przez gracza lub krupiera */
    function dobierzKarte($gracz) {
        $dobranaKarta = array_pop($_SESSION["talia"]);                              // Pobierz kartę z talii
        array_push($gracz->karty, $dobranaKarta);                                   // Dodaj ją do swojej kolekcji
        $_SESSION["suma" . ucfirst($gracz->nazwa) . "a"] += $dobranaKarta->wartosc; // Zaktualizuj sumę
    }

    /* Zakończenie gry i wyświetlenie wyniku */
    function zakonczGre($wygrany, $zdarzenie) {
        if($wygrany !== "remis") {
            $wygrany = $wygrany->nazwa;
            $trescWyniku = "wygrywa" . ucfirst($wygrany);   // Do zastosowania jako ID paragrafu z wynikiem
        }
        else {
            $trescWyniku = $wygrany;
        }
        $_SESSION["gracze"][0]->karty[1]->zakryta = false;  // Krupier odkrywa zakrytą kartę
        switch($zdarzenie) {
            case "fura":
                $komunikat = $wygrany === "remis" ? "Podwójna fura! Remis!" : "Fura! $wygrany wygrywa!";
                break;
            case "blackjack":
                $komunikat = $wygrany === "remis" ? "Podwójny blackjack! Remis!" : "Blackjack! $wygrany wygrywa!";
                break;
            case "perskieOczko":
                $komunikat = $wygrany === "remis" ? "Podwójne perskie oczko! Remis!" : "Perskie oczko! $wygrany wygrywa!";
                break;
            default:
                $komunikat = $wygrany === "remis" ? "Remis!" : "$wygrany wygrywa!";
        }
        $_SESSION["wynikGry"] = "<p>Krupier: " . $_SESSION["sumaKrupiera"] . "</p>Gracz: " . $_SESSION["sumaGracza"] . "</p><p id = '$trescWyniku'><strong>" . $komunikat . "</strong></p>";
        $_SESSION["graRozpoczeta"] = false;
        $_SESSION["graRozstrzygnieta"] = true;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gra w oczko!</title>
        <style>
            * {
                margin: 0;
                padding: 0;
            }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                text-align: center;
                font-family: "Candara", Geneva, Tahoma, sans-serif;
                background: url("https://images.unsplash.com/photo-1518895312237-a9e23508077d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2084&q=80");
                background-repeat: no-repeat;
                background-size: 100% 100%;
                background-position: center;
                background-attachment: fixed;

            }
            #baner {
                font-size: 3rem;
                line-height: 2;
                color: white;
            }
            #stolDoGry {
                width: 1000px;
                height: 500px;
                margin: 0 auto;
                border: 10px solid maroon;
                border-radius: 50px;
                background-color: darkgreen;
                overflow: hidden;
            }
            #poleKrupiera, #poleGracza {
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 50%;
                width: 100%;
                padding: 20px;
                box-sizing: border-box;
                color: white;
            }
            #poleKrupiera {
                flex-direction: column-reverse;
            }
            #poleGracza {
                flex-direction: column;
            }
            .zestawKart {
                display: flex;
                justify-content: center;
                width: 100%;
            }
            #kontrolki {
                display: flex;
                justify-content: center;
            }
            #kontrolki input {
                padding: 8px;
                font-size: 1.5rem;
                font-family: inherit;
            }
            #kontrolkiAkcji {
                margin-top: 8px;
                margin-bottom: 8px;
            }
            #kontrolkiAkcji input:not(:last-of-type) {
                margin-right: 4px;
            }
            #rozpocznijGre, #resetujGre {
                width: 100%;
            }
            .karta {
                width: 80px;
            }
            .karta:not(:last-of-type) {
                margin-right: -65px;
                filter: brightness(90%);
            }
            .karta:last-of-type {
                filter: brightness(100%);
            }
            #wynikGry {
                margin-top: 10px;
            }
            #remis {
                color: blue;
            }
            #wygrywaGracz {
                color: green;
            }
            #wygrywaKrupier {
                color: red;
            }
        </style>
    </head>
    <body>
        <main>
            <h1 id = "baner">Gra w oczko!</h1>
            <div id = "stolDoGry">
                <div id = "poleKrupiera">
                    <h3>Krupier</h3>
                    <div class = "zestawKart">
                        <?php
                            if($_SESSION["graRozpoczeta"] || $_SESSION["graRozstrzygnieta"]) {
                                wyswietlKarty($krupier);
                            }
                        ?>
                    </div>
                </div>
                <hr/>
                <div id = "poleGracza">
                    <h3>Gracz</h3>
                    <div class = "zestawKart">
                        <?php
                            if($_SESSION["graRozpoczeta"] || $_SESSION["graRozstrzygnieta"]) {
                                wyswietlKarty($gracz);
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div id = "kontrolki">
                <form method = "post">
                    <div id = "kontrolkiAkcji">
                        <input type = "submit" name = "dobierz" id = "dobierz" value = "Dobierz kartę">
                        <input type = "submit" name = "pasuj" id = "pasuj" value = "Pasuj">
                    </div>
                    <input type = "submit" name = "rozpocznijGre" id = "rozpocznijGre" value = "Rozpocznij grę" style = "display: <?php echo $_SESSION["graRozpoczeta"] || $_SESSION["graRozstrzygnieta"] ? "none" : "block" ?>">
                    <input type = "submit" name = "resetujGre" id = "resetujGre" value = "Resetuj" style = "display: <?php echo $_SESSION["graRozpoczeta"] || $_SESSION["graRozstrzygnieta"] ? "block" : "none" ?>">
                </form>
            </div>
            <div id = "wynikGry">
                <?php
                    if($_SESSION["graRozstrzygnieta"]) {
                        echo $_SESSION["wynikGry"];
                    }
                ?>
            </div>
        </main>
    </body>
</html>