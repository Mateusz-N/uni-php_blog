<aside>
    <?php
        /* Generowanie losowych obrazków po boku strony */
        $imgs = [];
        $img = "https://picsum.photos/";
        $img1 = "{$img}400/200";
        $img2 = "{$img}300";
        $img3 = "{$img}200/400";
        array_push($imgs, $img1, $img2, $img3);
        for($i = 0; $i < count($imgs); $i++) {
            echo "
                <h3>Sekcja {$i}</h3>
                <hr/>
                <img src = {$imgs[$i]} alt = \"Random image\" class = \"asideImg\">
            ";
        }
    ?>
</aside>