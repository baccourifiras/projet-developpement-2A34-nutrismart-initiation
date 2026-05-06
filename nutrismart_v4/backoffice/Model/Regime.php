<?php
/**
 * =====================================================================
 *  NutriSmart - Model/Regime.php
 *  Entite metier "Regime" (POO pure, aucune logique SQL ici).
 * =====================================================================
 */

class Regime
{
    /* --- Attributs prives ------------------------------------------- */
    private ?int   $id_regime;
    private string $type_regime;     // 'cut' | 'bulk' | 'equilibre'
    private int    $calories_cible;
    private string $date_debut;      // format Y-m-d
    private float  $poids_initial;
    private int    $duree;           // en jours

    /* ---------------------------------------------------------------- */
    public function __construct(
        string $type_regime    = 'equilibre',
        int    $calories_cible = 0,
        string $date_debut     = '',
        float  $poids_initial  = 0.0,
        int    $duree          = 0,
        ?int   $id_regime      = null
    ) {
        $this->type_regime    = $type_regime;
        $this->calories_cible = $calories_cible;
        $this->date_debut     = $date_debut;
        $this->poids_initial  = $poids_initial;
        $this->duree          = $duree;
        $this->id_regime      = $id_regime;
    }

    /* --- Getters ---------------------------------------------------- */
    public function getIdRegime(): ?int      { return $this->id_regime; }
    public function getTypeRegime(): string  { return $this->type_regime; }
    public function getCaloriesCible(): int  { return $this->calories_cible; }
    public function getDateDebut(): string   { return $this->date_debut; }
    public function getPoidsInitial(): float { return $this->poids_initial; }
    public function getDuree(): int          { return $this->duree; }

    /* --- Setters ---------------------------------------------------- */
    public function setIdRegime(?int $id): void       { $this->id_regime = $id; }
    public function setTypeRegime(string $t): void    { $this->type_regime = $t; }
    public function setCaloriesCible(int $c): void    { $this->calories_cible = $c; }
    public function setDateDebut(string $d): void     { $this->date_debut = $d; }
    public function setPoidsInitial(float $p): void   { $this->poids_initial = $p; }
    public function setDuree(int $d): void            { $this->duree = $d; }
}
