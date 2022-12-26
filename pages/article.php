<?php 
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>console.blog()</title>
        <link rel = "stylesheet" href = "./style.css" type = "text/css">
    </head>
    <body>
        <main id = "contentPreview">
            <?php
                /* Podgląd artykułu */
                if(isset($_POST["submitArticle"])) {
                    $tytul = $_POST["articleTitle"];
                    $kategoria = $_POST["articleCategory"];
                    $tresc = $_POST["articleText"];
                    $autor = "Anonim";  // Póki nie ma modułu kont użytkowników... — kiedy się pojawi, w to miejsce będzie pobierana nazwa zalogowanego użytkownika
                    $data = date("d/m/Y");
                    $wystapilBlad = false;
                    if(empty($tytul)) {
                        echo "Tytuł artykułu nie może być pusty!";
                        $wystapilBlad = true;
                    }
                    if(empty($kategoria)) {
                        echo "Kategoria artykułu nie może być pusta!";
                        $wystapilBlad = true;
                    }
                    if(empty($tresc)) {
                        echo "Treść artykułu nie może być pusta!";
                        $wystapilBlad = true;
                    }
                    if(!$wystapilBlad) {
                        $znacznikiBB = [
                            '/(\[b\])(.*?)(\[\/b\])/',
                            '/(\[i\])(.*?)(\[\/i\])/',
                            '/(\[u\])(.*?)(\[\/u\])/',
                            '/(\[s\])(.*?)(\[\/s\])/'
                        ];
                        $znacznikiHTML = [
                            '<strong>$2</strong>',
                            '<em>$2</em>',
                            '<u>$2</u>',
                            '<strike>$2</strike>'
                        ];
                        $tresc = preg_replace($znacznikiBB, $znacznikiHTML, $tresc);
                        echo '
                            <form action = "index.php" method = "post" id = "pipelineForm" novalidate>
                                <h1>Oto Twój artykuł:</h1>
                                <article>
                                    <header>
                                        <h1>'.$tytul.'</h1>
                                    </header>
                                    <main>
                                        <p>'.$tresc.'</p>
                                    </main>
                                    <footer>
                                        <span>Opublikowano '.$data.' przez '.$autor.'</span>
                                    </footer>
                                </article>
                                <input type = "hidden" name = "articleTitle" value = "'.$tytul.'">
                                <input type = "hidden" name = "articleCategory" value = "'.$kategoria.'">
                                <input type = "hidden" name = "articleText" value = "'.$tresc.'">
                                <input type = "hidden" name = "articleAuthor" value = "'.$autor.'">
                                <input type = "hidden" name = "articleDate" value = "'.$data.'">
                                <input type = "submit" name = "resumeArticleEdition" value = "Wróć do edycji" class = "previewSubmit">
                                <input type = "submit" name = "confirmArticle" value = "Zatwierdź" class = "previewSubmit">
                            </form>
                        ';
                    }
                }
            ?>
        </main>
    </body>
</html>