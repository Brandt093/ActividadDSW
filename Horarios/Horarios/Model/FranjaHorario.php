<?php
require_once 'modulo.php';

class FranjaHorario extends Modulo {
    public Semana $dia;
    public Hora $hora;
    public TipoFranja $tipoFranja;
    public Color $color;

    function __construct(Curso $curso, Clase $clase, Materia $materia, Semana $dia, Hora $hora, TipoFranja $tipoFranja, Color $color) {
        parent::__construct($curso, $clase, $materia);
        $this->dia = $dia;
        $this->hora = $hora;
        $this->tipoFranja = $tipoFranja;
        $this->color = $color;
    }
}
