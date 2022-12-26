<?php
    // Importy
    include_once "../common/php/classLibrary.php";
    include_once "../common/php/indexFunctions.php";
    include_once "../common/php/dbFunctions.php";
    session_start();
    $bazaPolaczenie = bazaPolacz();

/* * Poniższy kod musi zostać wykonany przed jakimkolwiek HTML-em, bo prowadzi do zmiany nagłówka! * */
    if(isset($_POST["signIn"])) {
        $uzytkownik = [];
        $uzytkownik["nazwa"] = $_POST["userName"];
        $uzytkownik["haslo"] = $_POST["password"];
        $idUzytkownika = weryfikujUzytkownikaWBazie($bazaPolaczenie, $uzytkownik);
        $uzytkownik["awatar"] = zwrocAwatar($bazaPolaczenie, $idUzytkownika);
        if($idUzytkownika != -1 ) { // -1 === logowanie nie powiodło się
            $_SESSION["userSession"]["id"] = $idUzytkownika;
            $_SESSION["userSession"]["name"] = $uzytkownik["nazwa"];
            $_SESSION["userSession"]["avatar"] = $uzytkownik["awatar"];
            $_SESSION["success"] = "Zalogowano pomyślnie!";
        }
        else {
            $_SESSION["exception"] = "Nie udało się zalogować. Upewnij się, że wpisujesz poprawne dane!";
        }
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);  // Proste POST-Redirect-GET (by odświeżenie strony nie wysłało ponownie formularza)
        exit;
    }
    if(isset($_POST["signOut"])) {
        unset($_SESSION["userSession"]);
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit;
    }
    if(isset($_POST["signUp"])) {
        $nowyUzytkownik = [];
        $nowyUzytkownik["nazwa"] = $_POST["userName"];
        $nowyUzytkownik["haslo"] = $_POST["password"];
        $nowyUzytkownik["hasloPotwierdzone"] = $_POST["passwordConfirm"];
        $nowyUzytkownik["email"] = $_POST["email"];
        if(!is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
            $awatar = "../resources/profile.jpg";
        }
        else {
            $awatar = $_FILES["avatar"]["tmp_name"];
        }
        $nowyUzytkownik["awatar"] = file_get_contents($awatar);
        if($nowyUzytkownik["haslo"] === $nowyUzytkownik["hasloPotwierdzone"]) {
            $wyjatek = dodajUzytkownikaDoBazy($bazaPolaczenie, $nowyUzytkownik);    // Jeśli wszystko OK, nic się nie stanie, jeśli nie — $exception przyjmie treść wyjątku
            if($wyjatek === false) {
                $_SESSION["success"] = "Konto utworzone pomyślnie!";
            }
            else {
                $_SESSION["exception"] = $wyjatek;
            }
        }
        else {
            $_SESSION["exception"] = "Wprowadzone hasła się nie zgadzają!";
        }
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit;
    }
    /* Dodanie komentarza napisanego przez użytkownika */
    if(isset($_POST["confirmComment"])) {
        $dodanyKomentarz = [];
        $dodanyKomentarz["idArtykulu"] = intval($_POST["articleID"]);
        $dodanyKomentarz["idAutora"] = intval($_SESSION["userSession"]["id"]);
        $dodanyKomentarz["tresc"] = $_POST["commentText"];
        $wyjatek = dodajKomentarzDoBazy($bazaPolaczenie, $dodanyKomentarz);
        if($wyjatek === false) {
            $_SESSION["success"] = "Komentarz dodany pomyślnie!";
        }
        else {
            $_SESSION["exception"] = $wyjatek;
        }
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit;
    }
    /* Dodanie artykułu napisanego przez użytkownika */
    if(isset($_POST["confirmArticle"])) {
        $dodanyArtykul = [];
        $dodanyArtykul["idAutora"] = $_SESSION["userSession"]["id"];
        $dodanyArtykul["tytul"] = $_POST["articleTitle"];
        $dodanyArtykul["tresc"] = $_POST["articleText"];
        $dodanyArtykul["kategoria"] = $_POST["articleCategory"];
        $wyjatek = dodajArtykulDoBazy($bazaPolaczenie, $dodanyArtykul);
        if($wyjatek === false) {
            $_SESSION["success"] = "Artykuł dodany pomyślnie!";
        }
        else {
            $_SESSION["exception"] = $wyjatek;
        }
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit;
    }
    /* Edycja wybranego artykułu przez jego autora */
    if(isset($_POST["editArticle"])) {
        $edytowanyArtykul = [];
        $edytowanyArtykul["id"] = $_POST["articleID"];
        $edytowanyArtykul["tytul"] = $_POST["articleTitle"];
        $edytowanyArtykul["tresc"] = $_POST["articleText"];
        $edytowanyArtykul["kategoria"] = $_POST["articleCategory"];
        $wyjatek = edytujArtykulWBazie($bazaPolaczenie, $edytowanyArtykul);
        if($wyjatek === false) {
            $_SESSION["success"] = "Artykuł zedytowany pomyślnie!";
        }
        else {
            $_SESSION["exception"] = $wyjatek;
        }
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit;
    }
    /* Usunięcie wybranego artykułu przez jego autora */
    if(isset($_POST["deleteArticleButton"])) {
        usunArtykulZBazy($bazaPolaczenie, intval($_POST["dbArticleID"]));
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit;
    }
    /* "Przechwycenie" formularza w celu zapisania go w sesji przed wysłaniem na stronę z podglądem */
    if(isset($_POST["submitArticle"])) {
        $title = $_POST["articleTitle"];
        $category = $_POST["articleCategory"];
        $text = $_POST["articleText"];
        $_SESSION["newArticle"] = new Artykul();
        $_SESSION["newArticle"]->setTytul($title);
        $_SESSION["newArticle"]->setKategoria($category);
        $_SESSION["newArticle"]->setTresc($text);
        echo '
            <form action = "article.php" method = "post" id = "pipelineForm" style = "display: none;">
                <input type = "hidden" name = "articleTitle" value = "' . $title . '">
                <input type = "hidden" name = "articleCategory" value = "' . $category . '">
                <input type = "hidden" name = "articleText" value = "' . $text . '">
                <input type = "hidden" name = "submitArticle" value = "confirmed">
            </form>
        ';
        ?>
        <script>
            document.getElementById("pipelineForm").submit();
        </script>
        <?php
    }
/* * --------------------------------------------------------------------------------------------- * */
?>
<!DOCTYPE html>
<html>
    <head>
        <title>console.blog()</title>
        <link rel = "stylesheet" href = "./style.css" type = "text/css">
        <script src = "../common/js/validate.js"></script>
        <script src = "../common/js/article.js"></script>
    </head>
    <body>
        <?php
            include_once "../components/navbar.php";
            include_once "../components/header.php";
        ?>
        <main>
            <section>
                <?php
                    if(isset($_SESSION["exception"])) {
                        echo "<p style = 'color: red; padding-bottom: 8px'>" . $_SESSION["exception"] . "</span>";
                        unset($_SESSION["exception"]);
                    }
                    if(isset($_SESSION["success"])) {
                        echo "<p style = 'color: limegreen; padding-bottom: 8px'>" . $_SESSION["success"] . "</span>";
                        unset($_SESSION["success"]);
                    }
                    
                    // Ćwiczenie 1
                    $A = rand(-10, 10);
                    $B = rand(-10, 10);
                    $C = rand(-10, 10);
                    $cw1 = rozwiazRownanieKwadratowe($A, $B, $C);

                    // Ćwiczenie 2
                    $cw2a = [silniaIteracyjna(15), silniaRekurencyjna(15)];
                    $cw2b = [silniaIteracyjna(200), silniaRekurencyjna(200)];

                    // Ćwiczenie 3
                    $zarobki = 12000;
                    $lokata = 0.05;
                    $docelowaSuma = 200000;
                    $czestotliwoscKapitalizacji = 1;
                    $cw3 = ileLatOszczedzania($zarobki, $lokata, $docelowaSuma, $czestotliwoscKapitalizacji);
                    $zarobki = number_format($zarobki, 0, ',', ' ');
                    $docelowaSuma = number_format($docelowaSuma, 0, ',', ' ');

                    // Ćwiczenie 4
                    $wysokoscChoinki = rand(4, 14);
                    $cw4 = "<span id=\"choinka\">".rysujChoinke($wysokoscChoinki)."</span>";

                    $artykuly = zwrocRekordyTabeli($bazaPolaczenie, "article");
                    $komentarze = zwrocRekordyTabeli($bazaPolaczenie, "comment");
                    $uzytkownicy = zwrocRekordyTabeli($bazaPolaczenie, "user");

                    /* Prowizoryczna paginacja */
                    $wynikowNaStrone = 3;
                    $wynikowRazem = count($artykuly);
                    $ileStron = $wynikowRazem > 0 ? intval(ceil($wynikowRazem / $wynikowNaStrone)) : 1;
                    $biezacaStrona = isset($_GET['str']) && is_numeric($_GET['str']) ? intval($_GET['str']) : 1;
                    $pierwszyNaStronie = ($biezacaStrona - 1) * $wynikowNaStrone;
                    $ostatniNaStronie = ($biezacaStrona === $ileStron) ? $wynikowRazem : ($pierwszyNaStronie + $wynikowNaStrone);

                    /* Sekcja nawigacji między stronami */
                    echo "<p>Strona:</p><p id = \"pageLinks\">";
                    if($biezacaStrona != 1) {
                        dodajLinkNaStrone($biezacaStrona - 1, "&#171;");
                    }
                    for($strona = 1; $strona <= $ileStron; $strona++) {
                        dodajLinkNaStrone($strona, $strona);
                    }
                    if($biezacaStrona != $ileStron) {
                        dodajLinkNaStrone($biezacaStrona + 1, "&#187;");
                    }
                    echo "</p>";

                    /* Generowanie artykułów */
                    $button_dodajArtykul = "<button class = 'addArticleButton' onclick = 'przelaczEdytorArtykulu(event, `create`, 0);'><img class = 'addArticleIcon' src = '../resources/article-editor/addArticleIcon.svg' alt = 'Nowy artykuł'>Nowy artykuł</button>";
                    if(isset($_SESSION["userSession"])) {
                        echo $button_dodajArtykul;
                    }
                    $button_usunArtykul = "<button type = 'submit' name = 'deleteArticleButton' class = 'deleteArticleButton' title = 'Usuń''><img class = 'deleteArticleIcon' src = '../resources/article-editor/deleteArticleIcon.svg' alt = 'Usuń artykuł'></button>";

                    for($i = $pierwszyNaStronie; $i < $ostatniNaStronie; $i++) {
                        $idArtykuluWBazie = $artykuly[$i]["article_id"];
                        $button_edytujArtykul = "<button type = 'button' class = 'editArticleButton' title = 'Edytuj' onclick = 'przelaczEdytorArtykulu(event, `edit`, $idArtykuluWBazie);'><img class = 'editArticleIcon' src = '../resources/article-editor/editArticleIcon.svg' alt = 'Edytuj artykuł'></button>";
                        echo '
                            <article>
                                <header>
                                    <h1 class = "articleTitle">' . $artykuly[$i]["title"] . '</h1>
                                    <form method = "post" class = "articleOptions"><input type = "hidden" name = "dbArticleID" value = "' . $idArtykuluWBazie . '">' . (isset($_SESSION["userSession"]) && $_SESSION["userSession"]["id"] === $artykuly[$i]["author_id"] ? $button_edytujArtykul . $button_usunArtykul : "") . '</form>
                                </header>
                                <main>
                                    <p class = "articleText">' . $artykuly[$i]["text"] . '</p>
                                    <section class = "commentSection">
                                        <div class = "commentSectionDivider">
                                            <h3>Komentarze</h3>
                                        </div>
                                        ' . generujKomentarze(zwrocSekcjeKomentarzy($bazaPolaczenie, $idArtykuluWBazie)) . '
                                    </section>
                                    ' . (isset($_SESSION["userSession"]) ? dodajKomentarz($idArtykuluWBazie) : "") . '
                                </main>
                                <footer>
                                    <span>Kategoria: <span class = "articleCategory">' . $artykuly[$i]["category"] . '</span></span><span>Opublikowano ' . $artykuly[$i]["submit_date"] . ' przez ' . zwrocAutora($bazaPolaczenie, $artykuly[$i]["author_id"]) .'</span>
                                </footer>
                            </article>
                        ';
                    }
                    if($wynikowRazem > 0 && isset($_SESSION["userSession"])) {
                        echo $button_dodajArtykul;
                    }

                    /* Przywróć stan edytora jeśli użytkownik wrócił do edycji */
                    if(isset($_POST["resumeArticleEdition"])) {
                        echo '
                            <script>
                                pokazEdytorArtykulu(document.getElementsByClassName("addArticleButton")[0], "create", "' . $_SESSION["newArticle"] -> tytul . '", "' . $_SESSION["newArticle"] -> kategoria . '", "' . $_SESSION["newArticle"] -> tresc . '", 0);
                                ustawPasekNarzedzi();
                            </script>
                        ';
                    }
                ?>
            </section>
            <?php 
                include_once "../components/aside.php";
            ?>
        </main>
        <?php
            include_once "../components/footer.php";
        ?>
    </body>
</html>
<?php
    bazaRozlacz($bazaPolaczenie);
?>