<?php
    class Artykul {
        public $autor;
        public $tytul;
        public $kategoria;
        public $tresc;
        public $data;
        public $komentarze = [];
        function setAutor($autor) {
            $this->autor = $autor;
        }
        function getAutor() {
            return $this->autor;
        }
        function setTytul($tytul) {
            $this->tytul = $tytul;
        }
        function getTytul() {
            return $this->tytul;
        }
        function setKategoria($kategoria) {
            $this->kategoria = $kategoria;
        }
        function getKategoria() {
            return $this->kategoria;
        }
        function setTresc($tresc) {
            $this->tresc = $tresc;
        }
        function getTresc() {
            return $this->tresc;
        }
        function setData($data) {
            $this->data = $data;
        }
        function getData() {
            return $this->data;
        }
        function setKomentarze($komentarze) {
            $this->komentarze = $komentarze;
        }
        function getKomentarze() {
            return $this->komentarze;
        }
        function addKomentarz($komentarz) {
            array_push($this->komentarze, $komentarz);
        }
    }
    class Komentarz {
        public $autor;
        public $tresc;
        public $awatar;
        public $data;
        function setAutor($autor) {
            $this->autor = $autor;
        }
        function getAutor() {
            return $this->autor;
        }
        function setTresc($tresc) {
            $this->tresc = $tresc;
        }
        function getTresc() {
            return $this->tresc;
        }
        function setAwatar($awatar) {
            $this->awatar = $awatar;
        }
        function getAwatar() {
            return $this->awatar;
        }
        function setData($data) {
            $this->data = $data;
        }
        function getData() {
            return $this->data;
        }
    }
?>