<?php

declare(strict_types=1);

class Regime
{
    private ?int $idRegime;
    private string $typeRegime;
    private int $caloriesCible;
    private string $dateDebut;
    private float $poidsInitial;
    private int $duree;

    public function __construct(
        ?int $idRegime,
        string $typeRegime,
        int $caloriesCible,
        string $dateDebut,
        float $poidsInitial,
        int $duree
    ) {
        $this->idRegime = $idRegime;
        $this->typeRegime = $typeRegime;
        $this->caloriesCible = $caloriesCible;
        $this->dateDebut = $dateDebut;
        $this->poidsInitial = $poidsInitial;
        $this->duree = $duree;
    }

    public function __destruct()
    {
    }

    public function getIdRegime(): ?int
    {
        return $this->idRegime;
    }

    public function setIdRegime(?int $idRegime): self
    {
        $this->idRegime = $idRegime;
        return $this;
    }

    public function getTypeRegime(): string
    {
        return $this->typeRegime;
    }

    public function setTypeRegime(string $typeRegime): self
    {
        $this->typeRegime = $typeRegime;
        return $this;
    }

    public function getCaloriesCible(): int
    {
        return $this->caloriesCible;
    }

    public function setCaloriesCible(int $caloriesCible): self
    {
        $this->caloriesCible = $caloriesCible;
        return $this;
    }

    public function getDateDebut(): string
    {
        return $this->dateDebut;
    }

    public function setDateDebut(string $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getPoidsInitial(): float
    {
        return $this->poidsInitial;
    }

    public function setPoidsInitial(float $poidsInitial): self
    {
        $this->poidsInitial = $poidsInitial;
        return $this;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id_regime' => $this->idRegime,
            'type_regime' => $this->typeRegime,
            'calories_cible' => $this->caloriesCible,
            'date_debut' => $this->dateDebut,
            'poids_initial' => $this->poidsInitial,
            'duree' => $this->duree,
        ];
    }
}
