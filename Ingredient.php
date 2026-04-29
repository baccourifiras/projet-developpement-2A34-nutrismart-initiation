<?php
/**
 * =====================================================================
 *  NutriSmart v3 — Model/Ingredient.php
 *  Entité métier pour un ingrédient.
 * =====================================================================
 */

declare(strict_types=1);

class Ingredient
{
    private ?int $idIngredient;
    private string $nom;
    private float $quantite;
    private string $unite;
    private ?int $idRecette;
    private ?string $imageUrl;

    public function __construct(
        string $nom = '',
        float $quantite = 0.0,
        string $unite = '',
        ?int $idRecette = null,
        ?int $idIngredient = null,
        ?string $imageUrl = null
    ) {
        $this->idIngredient = $idIngredient;
        $this->nom = $nom;
        $this->quantite = $quantite;
        $this->unite = $unite;
        $this->idRecette = $idRecette;
        $this->imageUrl = $imageUrl;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            $row['nom'] ?? '',
            isset($row['quantite']) ? (float) $row['quantite'] : 0.0,
            $row['unite'] ?? '',
            isset($row['id_recette']) ? (int) $row['id_recette'] : null,
            isset($row['id_ingredient']) ? (int) $row['id_ingredient'] : null,
            $row['image_url'] ?? null
        );
    }

    public function getIdIngredient(): ?int { return $this->idIngredient; }
    public function getNom(): string { return $this->nom; }
    public function getQuantite(): float { return $this->quantite; }
    public function getUnite(): string { return $this->unite; }
    public function getIdRecette(): ?int { return $this->idRecette; }
    public function getImageUrl(): ?string { return $this->imageUrl; }

    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setQuantite(float $quantite): void { $this->quantite = $quantite; }
    public function setUnite(string $unite): void { $this->unite = $unite; }
    public function setIdRecette(?int $idRecette): void { $this->idRecette = $idRecette; }
    public function setImageUrl(?string $imageUrl): void { $this->imageUrl = $imageUrl; }
}
