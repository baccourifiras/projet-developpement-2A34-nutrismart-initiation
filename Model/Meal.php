<?php

declare(strict_types=1);

class Meal
{
    private ?int $id;
    private string $type_repas;
    private int $calories;
    private string $type_regime;
    private string $contenu_genere;
    private string $date_creation;

    public function __construct(
        ?int $id,
        string $type_repas,
        int $calories,
        string $type_regime,
        string $contenu_genere,
        string $date_creation
    ) {
        $this->id = $id;
        $this->type_repas = $type_repas;
        $this->calories = $calories;
        $this->type_regime = $type_regime;
        $this->contenu_genere = $contenu_genere;
        $this->date_creation = $date_creation;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTypeRepas(): string
    {
        return $this->type_repas;
    }

    public function getCalories(): int
    {
        return $this->calories;
    }

    public function getTypeRegime(): string
    {
        return $this->type_regime;
    }

    public function getContenuGenere(): string
    {
        return $this->contenu_genere;
    }

    public function getDateCreation(): string
    {
        return $this->date_creation;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type_repas' => $this->type_repas,
            'calories' => $this->calories,
            'type_regime' => $this->type_regime,
            'contenu_genere' => $this->contenu_genere,
            'date_creation' => $this->date_creation,
        ];
    }
}
