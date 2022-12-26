<?php

    /* Zwrócenie komunikatu o błędzie pod niepoprawną captchą LUB zamiast podglądu komentarza w przypadku gdy dane z formularza są niepoprawne */
    function zwrocBlad($pole, $typ, $zaimek) {
        global $wystapilBlad;   // "Przechwyć" globalną zmienną $wystapilBlad
        $wystapilBlad = true;
        switch($typ) {
            case "nienaturalneZachowanie":  // Tylko w przypadku oblania reCAPTCHAv3
                $blad = "Wykryto nienaturalne zachowanie — reCAPTCHAv3 uznała, że jesteś botem!";
                break;
            case "pustePole":
                $blad = "{$pole} nie może być pust{$zaimek}";
                break;
            default:
                $blad = "Nieprawidłow{$zaimek} {$pole}";
        }
        echo "<p class = \"required\"><strong>Błąd:&nbsp;</strong>{$blad}!</p>";
    }

    /* Odczyt danych z formularza */
    function odczytajDane($poRazPierwszy) {
        global $idArtykulu;
        global $tresc;
        global $uzytkownik;
        global $email;
        global $data;
        global $awatar;
        global $captcha;
        global $rodzajCaptchy;
        $idArtykulu = $_POST["articleID"];
        $tresc = $_POST["commentText"];
        $uzytkownik = $_POST["commentUsername"];
        $email = $_POST["commentEmail"];
        $rodzajCaptchy = $_POST["captchaType"];
        if($poRazPierwszy) {    // Po naciśnięciu przycisku "Skomentuj"
            $tresc = trim($tresc);
            $uzytkownik = trim($uzytkownik);
            $email = trim($email);
            $data = date("d/m/Y");
            $awatar = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png";
            $rodzajCaptchy = trim($rodzajCaptchy);
        }
        else {                  // Przy wtórnym wysłaniu formularza, np. po rozwiązaniu captchy, wystarczy pobrać dane przekazane w ukrytych polach
            $data = $_POST["commentDate"];
            $awatar = $_POST["commentAvatar"];
            $captcha = trim($_POST["captchaAnswer"]);
        }
    }

    /* Ustawienie ukrytych pól odczytanymi wcześniej wartościami w celu automatycznego przekazania danych do następnego formularza  */
    function przekazDane($email, $uzytkownik, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy) {
        return '
            <input type = "hidden" name = "commentEmail" value = "'.$email.'">
            <input type = "hidden" name = "commentUsername" value = "'.$uzytkownik.'">
            <input type = "hidden" name = "commentText" value = "'.$tresc.'">
            <input type = "hidden" name = "commentDate" value = "'.$data.'">
            <input type = "hidden" name = "commentAvatar" value = "'.$awatar.'">
            <input type = "hidden" name = "articleID" value = "'.$idArtykulu.'">
            <input type = "hidden" name = "captchaType" value = "'.$rodzajCaptchy.'">
        ';
    }

    /* Generator captchy */
    function generujCaptche($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzaj) {
        $captchaHTML = "<p>Jeśli nie jesteś robotem, ";
        if($rodzaj === "recaptcha") {
            $wnetrzeFormularza = '
                <input
                    type = "submit"
                    value = "Zatwierdź"
                    class = "g-recaptcha previewSubmit"
                    data-sitekey = "6LclIQgjAAAAAL5zWw8-8SKu_KsP-g95xXdSo7w8"
                    data-callback = "triggerRecaptcha"
                    data-action = "submitRecaptcha"
                >
                <input type = "hidden" name = "captchaAnswer" value = "">
            ';
        }
        else {
            if($rodzaj === "obrazkowa") {
                $buzka = '
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="1" y="1" width="22" height="22" rx="7.656" style="fill:#f8de40"/>
                        <path d="M23 13.938a14.69 14.69 0 0 1-12.406 6.531c-5.542 0-6.563-1-9.142-2.529A7.66 7.66 0 0 0 8.656 23h6.688A7.656 7.656 0 0 0 23 15.344z" style="fill:#e7c930"/>
                        <path d="M16.53 12.324a8.617 8.617 0 0 1-.494.726 5.59 5.59 0 0 1-1.029 1.058 4.794 4.794 0 0 1-.6.412 1.6 1.6 0 0 1-.162.091c-.055.028-.109.061-.164.09-.115.051-.226.115-.346.163-.26.119-.533.223-.819.329a.231.231 0 0 0 .055.446 3.783 3.783 0 0 0 .979-.022 3.484 3.484 0 0 0 .878-.25 3.718 3.718 0 0 0 .409-.205l.012-.007a4.1 4.1 0 0 0 .379-.26 3.51 3.51 0 0 0 1.1-1.465 3.381 3.381 0 0 0 .222-.871c0-.031.006-.061.009-.092a.231.231 0 0 0-.429-.143z" style="fill:#864e20"/><path d="M21.554 5.693c-.063-.289-2.888-.829-4.871-.829a5.584 5.584 0 0 0-3.3.7A3.125 3.125 0 0 1 12 5.919a3.125 3.125 0 0 1-1.381-.352 5.584 5.584 0 0 0-3.3-.7c-1.983 0-4.808.54-4.871.829s-.113 1.217.088 1.381.439.025.477.6.477 2.976 1.808 3.767 3.741.163 4.6-.365A4.3 4.3 0 0 0 11.3 8.568c.138-.892.351-1.507.7-1.507s.565.615.7 1.507a4.3 4.3 0 0 0 1.883 2.51c.854.528 3.264 1.155 4.6.365s1.77-3.189 1.808-3.767.276-.439.477-.6.149-1.095.086-1.383z" style="fill:#101820"/>
                    </svg>
                ';
                $trojkat = '
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" xml:space="preserve">
                        <path fill="#FE6F80" d="M312.73 321.917H87.308c-6.577 0-12.659-3.973-16.016-9.616-3.364-5.647-3.505-12.905-.364-18.676L183.652 86.093c3.259-6 9.544-9.791 16.37-9.791 6.832 0 13.115 3.703 16.37 9.71l112.711 207.371c3.151 5.764 3 13.281-.354 18.925-3.363 5.642-9.442 9.609-16.019 9.609zm-194.074-41.924 164.438.026-83.072-151.425-81.366 151.399z"/><path fill="#FFA1AA" d="M93.534 312.3c-3.36-5.647-3.501-12.905-.36-18.676L205.891 86.093a18.945 18.945 0 0 1 5.247-6.079c-3.151-2.358-7.042-3.747-11.117-3.747a18.608 18.608 0 0 0-16.37 9.718L70.928 293.382c-3.141 5.764-3 13.275.364 18.925 3.357 5.647 9.44 9.61 16.016 9.61h22.243c-6.574 0-12.653-3.973-16.017-9.617z"/>
                        <g>
                            <path fill="#161616" d="M312.73 328.625H87.308c-7.69 0-15.672-5.07-20.34-12.915-4.287-7.193-4.474-17.372-.459-24.748l39.075-71.874a5.03 5.03 0 0 1 6.823-2.014 5.027 5.027 0 0 1 2.014 6.823l-39.075 71.877c-2.29 4.202-2.172 10.701.269 14.791 2.368 3.983 6.901 7.998 11.693 7.998H312.73c4.788 0 9.325-4.019 11.699-7.998 2.44-4.111 2.548-9.914.259-14.11L211.971 88.703c-2.43-4.484-7.009-7.265-11.948-7.265-5.018 0-9.597 2.689-11.948 7.022l-62.972 115.812c-1.323 2.443-4.386 3.341-6.823 2.014a5.028 5.028 0 0 1-2.014-6.823l62.968-115.806c4.107-7.576 12.076-12.284 20.789-12.284 8.634 0 16.599 4.8 20.792 12.526L333.526 291.64c3.963 7.261 3.786 16.937-.449 24.064-4.681 7.854-12.667 12.921-20.347 12.921zm-31.342-43.601H118.656c-1.769 0-3.41-.93-4.317-2.45a5.024 5.024 0 0 1-.115-4.962l81.366-151.399a5.033 5.033 0 0 1 8.864 0l81.366 151.399a5.02 5.02 0 0 1-.115 4.962 5.03 5.03 0 0 1-4.317 2.45zm-154.317-10.062h145.903l-72.951-135.739-72.952 135.739z"/>
                        </g>
                    </svg>
                ';
                $tablica = "<table id = \"imageCaptcha\">";
                $uklad = array_fill(1, 9, $trojkat);            // Układ to początkowo 9 trójkątów
                $idBuzki = rand(1, 9);                          // Następnie losowane jest ID buźki...
                $uklad[$idBuzki] = $buzka;                      // ...i trojkąt o tym ID zostaje zastąpiony buźką!
                $_SESSION["captchaAnswer"] = "$idBuzki";        // Zapisz prawidłowy wynik captchy w sesji przeglądarki
                for($wiersz = 1; $wiersz <= 3; $wiersz++) {
                    $tablica .= "<tr class = \"imageCaptchaRow\">";
                    for($kolumna = 1; $kolumna <= 3; $kolumna++) {
                        $idKomorki = $kolumna + 3 * ($wiersz - 1);
                        $tablica .= "<td class = \"imageCaptchaCell\" onclick = \"zaznaczKomorke(this, {$idKomorki})\"><span class = \"cellID\">{$idKomorki}</span>" . $uklad[$idKomorki] . "</td>";
                    }
                    $tablica .= "</tr>";
                }
                $tablica .= "</table>";
                $captchaHTML .= '
                    kliknij na <em>buźkę z okularami</em> poniżej:</p>'.
                    $tablica.
                    '<input type = "submit" name = "reloadCaptcha" value = "" id = "reloadCaptcha" title = "Nowa tablica">
                    <input type = "hidden" name = "captchaAnswer" value = "" id = "imageCaptchaAnswer">
                ';
            }
            elseif($rodzaj === "matematyczna") {
                $operatory = ["/" => "podzielić na", "*" => "razy", "+" => "dodać", "-" => "odjąć"];
                $cyfry = ["zero", "jeden", "dwa", "trzy", "cztery", "pięć", "sześć", "siedem", "osiem", "dziewięć", "dziesięć"];
                $operator = array_keys($operatory)[rand(0, count($operatory) - 1)];
                $wynik = null;
                do {                    // Generator działań do rozwiązania
                    $x = rand(0, 9);
                    $y = rand($operator === "/" ? 1 : 0, 9);    // Uniknięcie dzielenia przez zero
                    switch($operator) {
                        case "/":
                            $wynik = $x / $y;
                            break;
                        case "*":
                            $wynik = $x * $y;
                            break;
                        case "+":
                            $wynik = $x + $y;
                            break;
                        case "-":
                            $wynik = $x - $y;
                            break;
                    }
                }
                while($wynik < 0 || $wynik > 10 || !is_int($wynik));    // Wynik musi być z przedziału [0, 10] i być liczbą całkowitą
                $xSlownie = $cyfry[$x];
                $ySlownie = $cyfry[$y];
                $operatorSlownie = $operatory[$operator];
                $_SESSION["captchaAnswer"] = $cyfry[$wynik];            // Zapisz prawidłowy wynik captchy w sesji przeglądarki
                $captchaHTML .= '
                    wpisz <em>słownie</em> wynik poniższego działania:</p>
                    <h4 id = "captchaOperation">'.$xSlownie.' '.$operatorSlownie.' '.$ySlownie.'</h4>
                    <div id = "captchaAnswerContainer">
                        <label for = "captchaAnswer">Wynik:&nbsp;</label>
                        <input name = "captchaAnswer" placeholder = "'.$cyfry[rand(0, 9)].'" class = "formInput">
                        <input type = "submit" name = "reloadCaptcha" value = "" id = "reloadCaptcha" title = "Nowe działanie">
                    </div>
                ';
            }
            $wnetrzeFormularza = '
                <input
                    type = "submit"
                    value = "Zatwierdź"
                    class = "previewSubmit"
                    name = "confirmComment"
                >
                <fieldset id = "captchaFieldset">
                    <legend><span class = "required">*</span> Captcha</legend>
                    '.$captchaHTML.'
                </fieldset>
            ';
        }

        /*  Wyświetl captchę, która tak naprawdę jest formularzem. */
        /*  Pola ukryte służą jedynie do przekazania już odczytanych danych do kolejnych celów.
            Zarówno wylosowanie nowego działania, jak i zatwierdzenie komentarza, spowoduje ponowne wysłanie formularza na tę samą stronę z podglądem komentarza.
            Efektywnie, strona zostanie po prostu odświeżona z zebranymi już danymi.
            Losując nowe działanie, pojawi się nowe działanie, bo jest ono losowane przy każdorazowym załadowaniu strony.
            Zatwierdzając komentarz, sprawdzona zostanie poprawność captchy (patrz: isset($_POST["confirmComment"])...) */
        return '
            <form method = "post" novalidate id = "captchaForm">
                '.przekazDane($email, $uzytkownik, $tresc, $data, $awatar, $idArtykulu, $rodzaj).'
                '.$wnetrzeFormularza.'
            </form>
        ';
    }

    /* Wyświetlenie zawartości strony, czyli podglądu komentarza wraz z captchą i przyciskiem potwierdzenia */
    function wyswietlPodglad($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy) {
        echo '
            <h1>Oto Twój komentarz:</h1>
            <div class = "comment" style = "text-align: justify;">
                <figure>
                    <img src = '.$awatar.' alt = '.$email.' height = "100px" width = "100px" class = "commentAvatar" />
                    <figcaption><strong>'.$uzytkownik.' ('.$email.')</strong></figcaption>
                </figure>
                <p>'.$tresc.'</p>
                <div style = "clear: both; height: 0;"></div>
                <p style = "font-size: 0.75rem; text-align: right">Opublikowano: '.$data.'</p>
            </div>
        ';
        echo generujCaptche($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
    }

    /* Wysłanie komentarza spowrotem na główną stronę poprzez ukryty formularz */
    /* (prowizoryczne dodawanie komentarzy — działa dopóki użytkownik nie zamknie strony; widoczne tylko dla komentującego) */
    function przekierujDoGlownej($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy) {
        echo '
            <form action = "index.php" method = "post" id = "pipelineForm" style = "display: none;">
                '.przekazDane($email, $uzytkownik, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy).'
                <input type = "hidden" name = "confirmComment" value = "confirmed">
            </form>
        ';
        ?>
        <script>
            document.getElementById("pipelineForm").submit();
        </script>
        <?php
    }

?>