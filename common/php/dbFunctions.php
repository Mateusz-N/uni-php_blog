<?php
    function bazaPolacz() {
        $serwer = "localhost";
        $uzytkownik = "root";
        $haslo = "";
        $baza = "blog";
        $polaczenie = new mysqli($serwer, $uzytkownik, $haslo, $baza);
        if($polaczenie -> connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn -> connect_error);
        }
        return $polaczenie;
    }
    function zwrocRekordyTabeli($polaczenie, $nazwaTabeli) {
        $sql = "SELECT * FROM $nazwaTabeli";
        $wynik = $polaczenie -> query($sql);
        $tablicaRekordow = [];
        if($wynik -> num_rows > 0) {
            while($rekord = $wynik -> fetch_assoc()) {
                array_push($tablicaRekordow, $rekord);
            }
        }
        return $tablicaRekordow;
    }
    function zwrocAutora($polaczenie, $idAutora) {
        $sql = "SELECT name FROM user WHERE user_id = $idAutora";
        $wynik = $polaczenie -> query($sql);
        $rekord = $wynik -> fetch_assoc();
        return $rekord["name"];
    }
    function zwrocAwatar($polaczenie, $idAutora) {
        $sql = "SELECT avatar FROM user WHERE user_id = $idAutora";
        $wynik = $polaczenie -> query($sql);
        $rekord = $wynik -> fetch_assoc();
        return $rekord["avatar"];
    }
    function zwrocSekcjeKomentarzy($polaczenie, $idArtykulu) {
        $sql = "SELECT * FROM comment WHERE article_id = $idArtykulu";
        $wynik = $polaczenie -> query($sql);
        $komentarze = [];
        if($wynik -> num_rows > 0) {
            while($komentarz = $wynik -> fetch_assoc()) {
                $komentarz["avatar"] = zwrocAwatar($polaczenie, $komentarz["author_id"]);
                $komentarz["author_name"] = zwrocAutora($polaczenie, $komentarz["author_id"]);
                array_push($komentarze, $komentarz);
            }
        }
        return $komentarze;
    }
    function wykonajTransakcje($polaczenie, $sql) {
        $polaczenie -> query("START TRANSACTION");
        try {
            $polaczenie -> query($sql);
            if($polaczenie -> affected_rows == 0) {
                throw new Exception("Coś poszło nie tak!");
            }
            $polaczenie -> query("COMMIT");
            return false;   // $exception = false
        }
        catch(Exception $e) {
            $polaczenie -> query("ROLLBACK");
            return $e -> getMessage();
        }
    }
    function weryfikujUzytkownikaWBazie($polaczenie, $uzytkownik) {
        $sql = 'SELECT user_id FROM user WHERE name = "' . $uzytkownik["nazwa"] . '" && password = "' . $uzytkownik["haslo"] . '"';
        $wynik = $polaczenie -> query($sql);
        if($wynik -> num_rows == 0) {
            $wynik = -1;
        }
        else {
            $wynik = $wynik -> fetch_assoc()["user_id"];
        }
        return $wynik;
    }
    function dodajUzytkownikaDoBazy($polaczenie, $uzytkownik) {
        $sql = 'INSERT INTO user VALUES(NULL, "' . $uzytkownik["nazwa"] . '", "' . $uzytkownik["haslo"] . '", "' . $uzytkownik["email"] . '", "' . $polaczenie -> real_escape_string($uzytkownik["awatar"]) . '")';
        return wykonajTransakcje($polaczenie, $sql);
    }
    function dodajKomentarzDoBazy($polaczenie, $komentarz) {
        $sql = 'INSERT INTO comment VALUES(NULL, ' . $komentarz['idArtykulu'] . ', ' . $komentarz['idAutora'] . ', "' . $komentarz['tresc'] . '", CURDATE())';
        return wykonajTransakcje($polaczenie, $sql);
    }
    function dodajArtykulDoBazy($polaczenie, $artykul) {
        $sql = 'INSERT INTO article VALUES(NULL, ' . $artykul['idAutora'] . ', "' . $artykul['tytul'] . '", "' . $artykul['tresc'] . '", "' . $artykul['kategoria'] . '", CURDATE())';
        return wykonajTransakcje($polaczenie, $sql);
    }
    function edytujArtykulWBazie($polaczenie, $artykul) {
        $sql = 'UPDATE article SET title = "' . $artykul['tytul'] . '", text = "' . $artykul['tresc'] . '", category = "' . $artykul['kategoria'] . '" WHERE article_id = ' . $artykul['id'];
        return wykonajTransakcje($polaczenie, $sql);
    }
    function usunArtykulZBazy($polaczenie, $idArtykulu) {
        $sql = 'DELETE FROM article WHERE article_id = ' . $idArtykulu;
        return wykonajTransakcje($polaczenie, $sql);
    }
    function bazaRozlacz($polaczenie) {
        $polaczenie -> close();
    }
?>