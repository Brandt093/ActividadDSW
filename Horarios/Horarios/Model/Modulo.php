<?php
class Modulo {
    public Curso $curso;
    public Clase $clase;
    public Materia $materia;

    function __construct(Curso $curso, Clase $clase, Materia $materia) {
        $this->curso = $curso;
        $this->clase = $clase;
        $this->materia = $materia;
    }
}
