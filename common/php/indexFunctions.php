<?php

    // Ćwiczenie 1
    function rozwiazRownanieKwadratowe($A, $B, $C) {
        $wynikStr = "Wynik: ";
        $x0; $x1; $x2;
        if($A != 0) {
            $delta = pow($B, 2) - 4*$A*$C;
            if($delta == 0) {
                $x0 = (-$B)/(2*$A);
                return "{$wynikStr}{$x0}";
            }
            elseif($delta > 0) {
                $x1 = (-$B-sqrt($delta))/(2*$A);
                $x2 = (-$B+sqrt($delta))/(2*$A);
                return "{$wynikStr}x1 = {$x1}, x2 = {$x2}";
            }
            else {
                return "Delta ujemna, brak rozwiązań!";
            }
        }
        else {
            return "Funkcja nie jest kwadratowa!";
        }
    }

    // Ćwiczenie 2
    function silniaIteracyjna($n) {
        if($n < 0) {
            return "Silnia z liczby ujemnej jest niezdefiniowana!";
        }
        $wynik = 1;
        for($i = 2; $i <= $n; $i++) {
            $wynik *= $i;
        }
        return $wynik;
    }

    function silniaRekurencyjna($n) {
        return ($n > 0) ? $n * silniaRekurencyjna($n-1) : (($n < 0) ? "Silnia z liczby ujemnej jest niezdefiniowana!" : 1);
    }

    // Ćwiczenie 3
    function ileLatOszczedzania($zarobki, $lokata, $docelowaSuma, $czestotliwoscKapitalizacji) {    // $czestotliwoscKapitalizacji - co ile miesięcy
        $stanKonta = $zarobki;
        $mnoznik = 1 + $lokata;
        $miesiecyOszczedzania = 0;
        while($stanKonta < $docelowaSuma) {
            $stanKonta *= $mnoznik;
            $miesiecyOszczedzania++;
        }
        $wynik = new stdClass;
        $wynik -> lat = floor($miesiecyOszczedzania / 12);
        $wynik -> miesiecy = $miesiecyOszczedzania % 12;
        return $wynik;
    }

    // Ćwiczenie 4
    function rysujChoinke($wysokosc) {
        $szerokosc = 2 * $wysokosc - 3;
        $spacjeWObecnymWierszu = str_repeat("&nbsp;", ($szerokosc - 1) / 2);
        $spacjeWOstatnimWierszu = substr($spacjeWObecnymWierszu, 0, -6);    // "&nbsp;".length == 6
        $gwiazdkiWObecnymWierszu = "*";
        $wynik = $spacjeWObecnymWierszu.$gwiazdkiWObecnymWierszu.$spacjeWObecnymWierszu;
        for($wiersz = 1; $wiersz < $wysokosc - 1; $wiersz++) {
            $spacjeWObecnymWierszu = substr($spacjeWObecnymWierszu, 0, -6); 
            $gwiazdkiWObecnymWierszu = "*".$gwiazdkiWObecnymWierszu."*";    // Dodaj gwiazdkę po lewej i prawej
            $wynik .= "<br/>".$spacjeWObecnymWierszu.$gwiazdkiWObecnymWierszu.$spacjeWObecnymWierszu;
        }
        return $wynik."</br>".$spacjeWOstatnimWierszu."***".$spacjeWOstatnimWierszu;
    }

    /* Generowanie komentarzy pod artykułem */
    function generujKomentarze($komentarze) {
        $sekcjaKomentarzy = "";
        if(empty($komentarze)) {
            $sekcjaKomentarzy = "<p style = \"text-align: center; padding: 32px 0;\"><em>Cicho wszędzie, głucho wszędzie...</em></p>";
        }
        else {
            for($i = 0; $i < count($komentarze); $i++) {
                $sekcjaKomentarzy .= '
                    <div class = "comment">
                        <figure>
                            <img src = "data:image/jpeg;base64,' . base64_encode($komentarze[$i]["avatar"]) . '" alt = "' . $komentarze[$i]["author_name"] . '" height = "100px" width = "100px" class = "commentAvatar" />
                            <figcaption><strong>' . $komentarze[$i]["author_name"] . '</strong></figcaption>
                        </figure>
                        <p>' . $komentarze[$i]["text"] . '</p>
                        <div style = "clear: both; height: 0;"></div>
                        <p style = "font-size: 0.75rem; text-align: right">Opublikowano: ' . $komentarze[$i]["submit_date"] . '</p>
                    </div>
                '; 
            }
        }
        return $sekcjaKomentarzy;
    }

    /* Formularz dodający komentarz */
    function dodajKomentarz($idArtykulu) {
        $rodzajeCaptchy = ["matematyczna", "obrazkowa", "recaptcha"];
        return '
            <form action = "comment.php" method = "post" onsubmit = "return walidacjaJS(event);" class = "commentForm" novalidate>
                <label for = "commentText"  class = "labelAbove"><span class = "required">*</span> Treść komentarza</label>
                <div class = "commentTextContainer">
                    <textarea name = "commentText" rows = "8" placeholder = "Autor tego artykułu to..." oninput = "ukryjBlad(event);" class = "commentText formTextArea"></textarea>
                    <p class = "formFieldError formFieldErrorOver commentTextError"></p>
                </div>
                <div class = "commentBottomSection">
                    <label for = "commentUsername" class = "commentAuthorFieldLabel"><span class = "required">*</span> Nazwa użytkownika:</label>
                    <div class = "commentAuthorFieldContainer">
                        <input name = "commentUsername" placeholder = "JohnDoe2" oninput = "ukryjBlad(event);" class = "commentUsername commentAuthorField formInput">
                        <p class = "formFieldError formFieldErrorOver commentUsernameError"></p>
                    </div>
                    <label for = "commentEmail" class = "commentAuthorFieldLabel"><span class = "required">*</span> Email:</label>
                    <div class = "commentAuthorFieldContainer">
                        <input type = "email" name = "commentEmail" placeholder = "example@mail.com" oninput = "ukryjBlad(event);" class = "commentEmail commentAuthorField formInput">
                        <p class = "formFieldError formFieldErrorUnder commentEmailError"></p>
                    </div>
                    <input type = "hidden" name = "articleID" value = "'.$idArtykulu.'">
                    <input type = "hidden" name = "captchaType" value = "'.$rodzajeCaptchy[rand(0, 2)].'">
                    <input type = "submit" name = "submitComment" value = "Skomentuj" class = "submitButton submitButton_comment">
                </div>
            </form>
        ';
    }

    /* Prowizoryczna paginacja */
    function dodajLinkNaStrone($strona, $alias) {
        global $biezacaStrona;
        $zaznaczBiezaca = ($strona == $biezacaStrona) ? ' id = "pageLinkCurrent"' : '';
        echo '<a href = "index.php?str=' . $strona . '" class = "pageLink"'.$zaznaczBiezaca.'>' . $alias . '</a>';
    }

?>