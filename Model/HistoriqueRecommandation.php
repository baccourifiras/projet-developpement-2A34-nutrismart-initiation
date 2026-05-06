<?php

declare(strict_types=1);

class HistoriqueRecommandation
{
    private ?int $idHistorique;
    private int $idRegime;
    private string $recommandation;
    private string $date;

    public function __construct(
        ?int $idHistorique,
        int $idRegime,
        string $recommandation,
        string $date
    ) {
        $this->idHistorique = $idHistorique;
        $this->idRegime = $idRegime;
        $this->recommandation = $recommandation;
        $this->date = $date;
    }

    public function __destruct()
    {
    }

    public function getIdHistorique(): ?int
    {
        return $this->idHistorique;
    }

    public function setIdHistorique(?int $idHistorique): self
    {
        $this->idHistorique = $idHistorique;
        return $this;
    }

    public function getIdRegime(): int
    {
        return $this->idRegime;
    }

    public function setIdRegime(int $idRegime): self
    {
        $this->idRegime = $idRegime;
        return $this;
    }

    public function getRecommandation(): string
    {
        return $this->recommandation;
    }

    public function setRecommandation(string $recommandation): self
    {
        $this->recommandation = $recommandation;
        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id_historique' => $this->idHistorique,
            'id_regime' => $this->idRegime,
            'recommandation' => $this->recommandation,
            'date' => $this->date,
        ];
    }
}
