<?php

declare(strict_types=1);

class SuiviRegime
{
    private ?int $idSuivi;
    private int $idRegime;
    private string $date;
    private float $poids;
    private int $caloriesConsommees;

    public function __construct(
        ?int $idSuivi,
        int $idRegime,
        string $date,
        float $poids,
        int $caloriesConsommees
    ) {
        $this->idSuivi = $idSuivi;
        $this->idRegime = $idRegime;
        $this->date = $date;
        $this->poids = $poids;
        $this->caloriesConsommees = $caloriesConsommees;
    }

    public function __destruct()
    {
    }

    public function getIdSuivi(): ?int
    {
        return $this->idSuivi;
    }

    public function setIdSuivi(?int $idSuivi): self
    {
        $this->idSuivi = $idSuivi;
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

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getPoids(): float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): self
    {
        $this->poids = $poids;
        return $this;
    }

    public function getCaloriesConsommees(): int
    {
        return $this->caloriesConsommees;
    }

    public function setCaloriesConsommees(int $caloriesConsommees): self
    {
        $this->caloriesConsommees = $caloriesConsommees;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id_suivi' => $this->idSuivi,
            'id_regime' => $this->idRegime,
            'date' => $this->date,
            'poids' => $this->poids,
            'calories_consommees' => $this->caloriesConsommees,
        ];
    }
}
