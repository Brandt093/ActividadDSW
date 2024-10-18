<?php

enum Hora: string
{
    // Horario de Mañana:
    case Primera = '8:00 - 8:55';
    case Segunda = '8:55 - 9:50';
    case Tercera = '9:50 - 10:45';
    case Cuarta = '10:45 - 11:15';
    case Quinta = '11:15 - 12:10';
    case Sexta = "12:10 - 13:05";
    case Septima = "13:05 - 14:00";
    public function getHoraString(): string
    {
        return match($this) {
            self::Primera => '8:00 - 8:55',
            self::Segunda => '8:55 - 9:50',
            self::Tercera => '9:50 - 10:45',
            self::Cuarta => '10:45 - 11:15',
            self::Quinta => '11:15 - 12:10',
            self::Sexta => "12:10 - 13:05",
            self::Septima => "13:05 - 14:00",
            self::Octava => "14:30 - 15:25",
            self::Novena => "15:25 - 16:20",
            self::Decima => "16:20 - 17:15",
            self::Onceava => "17:15 - 17:45",
            self::Doceava => "17:45 - 18:40",
            self::Treceava => "18:40 - 19:35",
            self::Catorceava => "19:35 - 20:30",
        };}
    // Horario de Tarde:
    case Octava = "14:30 - 15:25";
    case Novena = "15:25 - 16:20";
    case Decima = "16:20 - 17:15";
    case Onceava = "17:15 - 17:45";
    case Doceava = "17:45 - 18:40";
    case Treceava = "18:40 - 19:35";
    case Catorceava = "19:35 - 20:30";

    public function codigoHora(): string
    {
        return match ($this) {
            static::Primera => '1',
            static::Segunda => '2',
            static::Tercera => '3',
            static::Cuarta => '4',
            static::Quinta => '5',
            static::Sexta => '6',
            static::Septima => '7',
            static::Octava => '8',
            static::Novena => '9',
            static::Decima => '10',
            static::Onceava => '11',
            static::Doceava => '12',
            static::Treceava => '13',
            static::Catorceava => '14',
        };
    }
}

enum Semana: string
{
    case Lunes = 'L';
    case Martes = 'M';
    case Miércoles = 'X';
    case Jueves = 'J';
    case Viernes = 'V';
}

enum Clase: string
{
    case R1 = 'R01';
    case R2 = 'R02';
    case R3 = 'R03';
    case R4 = 'R04';
    case C205 = 'C205';
}

enum Curso: string
{
    case DAW_1A = '1ADAW';
    case DAW_2A = '2ADAW';
    case DAM_1A = '1ADAM';
    case DAM_2A = '2ADAM';
    case DAM_1B = '1BDAM';
    case DAM_2B = '2BDAM';
}

enum Color: string
{
    case Rojo = '#E53229';
    case Azul = '#3A9DD6';
    case Verde = '#29E564';
    case Naranja = '#FC9C1F';
    case Rosa = '#edaaeb';
    case Amarillo = '#f1ef16';
    case Blanco = "#ffffff";
    case AzulClaro = '#87CEEB';
    case AzulOscuro = '#032B44';
    case VerdeClaro = '#8BC34A';
    case VerdeOscuro = '#2E865F';
    case NaranjaClaro = '#FFC107';
}

enum Materia: string
{
    case DSW = 'DSW';
    case DOR = 'DOR';
    case DEW = 'DEW';
    case PRW = 'PRW';
    case BAE = 'BAE';
    case PRO = 'PRO';
    case OTRO = 'OT';
    case TUTORÍA = 'TUO';
    case COTUTORIA = 'COTUTO';
    case GUARDIA = 'G';
    case REUNIÓN_DEPARTAMENTO = 'RD';
    case RECREO = 'RE';
}

enum TipoFranja: string
{
    case Lectiva = 'L';
    case Complementaria = 'C';
    case Recreo = 'RE';
}

enum TiposHorarios: string
{
    case Mañana = "M";
    case Tarde = "T";
    case Mixto = "MX";
}