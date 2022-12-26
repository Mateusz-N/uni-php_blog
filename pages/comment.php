<?php 
    include_once "../common/php/commentFunctions.php";
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>console.blog()</title>
        <link rel = "stylesheet" href = "./style.css" type = "text/css">
        <script src = "https://www.google.com/recaptcha/api.js"></script>
        <script src = "../common/js/captcha.js"></script>
    </head>
    <body>
        <main id = "contentPreview">
            <?php
                $wystapilBlad = false;
                /* Walidacja komentarza */
                if(isset($_POST["submitComment"])) {
                    odczytajDane(true);
                    if(empty($uzytkownik)) {
                        zwrocBlad("Nazwa użytkownika", "pustePole", "a");
                    }
                    if(empty($email)) {
                        zwrocBlad("E-mail", "pustePole", "y");
                    }
                    if(empty($tresc)) {
                        zwrocBlad("Treść komentarza", "pustePole", "a");
                    }
                    if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
                        zwrocBlad("E-mail", "nieprawidlowyFormat", "y");
                    }
                    if(!$wystapilBlad) {
                        wyswietlPodglad($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                    }
                }
                /* Ponowne wyświetlenie strony po wylosowaniu nowej captchy */
                if(isset($_POST["reloadCaptcha"])) {
                    odczytajDane(false);                // Walidacja nie jest już potrzebna, bo dane są spreparowane
                    wyswietlPodglad($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                }
                /* Walidacja captchy */
                if(isset($_POST["confirmComment"])) {
                    odczytajDane(false);
                    if(empty($captcha)) {
                        wyswietlPodglad($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                        zwrocBlad("Captcha", "pustePole", "a");
                    }
                    elseif($captcha !== $_SESSION["captchaAnswer"]) {
                        wyswietlPodglad($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                        zwrocBlad("Captcha", "nieprawidłowyWynik", "a");
                    }
                    if(!$wystapilBlad) {
                        przekierujDoGlownej($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                    }
                }
                if(isset($_POST["g-recaptcha-response"])) { // reCAPTCHA niestety narzuca własny POST
                    odczytajDane(false);                    
                    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
                    $recaptcha_secret = "6LclIQgjAAAAAHET2LsrCiRWMp5nTppshj1xGmJe";
                    $recaptcha_response = $_POST["g-recaptcha-response"];
                    $recaptcha = file_get_contents($recaptcha_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
                    $recaptcha = json_decode($recaptcha, true);
                    if($recaptcha["success"] && $recaptcha["score"] >= 0.5 && $recaptcha["action"] == "submitRecaptcha") {
                        przekierujDoGlownej($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                    }
                    else {
                        wyswietlPodglad($uzytkownik, $email, $tresc, $data, $awatar, $idArtykulu, $rodzajCaptchy);
                        zwrocBlad("Captcha", "nienaturalneZachowanie", "");
                    }
                }
            ?>
        </main>
    </body>
</html>