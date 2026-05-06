<?php
/**
 * =====================================================================
 *  NutriSmart - Model/Suivi.php
 *  Entite metier "Suivi_regime".
 *  Relie a un regime par la cle etrangere id_regime.
 * =====================================================================
 */

class Suivi
{
    /* --- Attributs prives ------------------------------------------- */
    private ?int   $id_suivi;
    private int    $id_regime;       // FK
    private string $date;            // format Y-m-d
    private float  $poids;
    private int    $calories_consommees;

    /* ---------------------------------------------------------------- */
    public function __construct(
        int    $id_regime           = 0,
        string $date                = '',
        float  $poids               = 0.0,
        int    $calories_consommees = 0,
        ?int   $id_suivi            = null
    ) {
        $this->id_regime           = $id_regime;
        $this->date                = $date;
        $this->poids               = $poids;
        $this->calories_consommees = $calories_consommees;
        $this->id_suivi            = $id_suivi;
    }

    /* --- Getters ---------------------------------------------------- */
    public function getIdSuivi(): ?int          { return $this->id_suivi; }
    public function getIdRegime(): int          { return $this->id_regime; }
    public function getDate(): string           { return $this->date; }
    public function getPoids(): float           { return $this->poids; }
    public function getCaloriesConsommees(): int { return $this->calories_consommees; }

    /* --- Setters ---------------------------------------------------- */
    public function setIdSuivi(?int $id): void         { $this->id_suivi = $id; }
    public function setIdRegime(int $idR): void        { $this->id_regime = $idR; }
    public function setDate(string $d): void           { $this->date = $d; }
    public function setPoids(float $p): void           { $this->poids = $p; }
    public function setCaloriesConsommees(int $c): void { $this->calories_consommees = $c; }
}
