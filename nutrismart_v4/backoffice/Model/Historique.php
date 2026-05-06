<?php
/**
 * =====================================================================
 *  NutriSmart - Model/Historique.php
 *  Entite metier "Historique_recommandation".
 *  Relie a un regime par la cle etrangere id_regime.
 * =====================================================================
 */

class Historique
{
    /* --- Attributs prives ------------------------------------------- */
    private ?int   $id_historique;
    private int    $id_regime;       // FK
    private string $recommandation;
    private string $date;            // format Y-m-d

    /* ---------------------------------------------------------------- */
    public function __construct(
        int    $id_regime      = 0,
        string $recommandation = '',
        string $date           = '',
        ?int   $id_historique  = null
    ) {
        $this->id_regime      = $id_regime;
        $this->recommandation = $recommandation;
        $this->date           = $date;
        $this->id_historique  = $id_historique;
    }

    /* --- Getters ---------------------------------------------------- */
    public function getIdHistorique(): ?int   { return $this->id_historique; }
    public function getIdRegime(): int        { return $this->id_regime; }
    public function getRecommandation(): string { return $this->recommandation; }
    public function getDate(): string         { return $this->date; }

    /* --- Setters ---------------------------------------------------- */
    public function setIdHistorique(?int $id): void { $this->id_historique = $id; }
    public function setIdRegime(int $idR): void     { $this->id_regime = $idR; }
    public function setRecommandation(string $r): void { $this->recommandation = $r; }
    public function setDate(string $d): void        { $this->date = $d; }
}
