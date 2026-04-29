<?php
/**
 * =====================================================================
 *  NutriSmart v3 — Model/Recipe.php
 *  Entité métier pure (aucune logique SQL).
 *  Remplace Recette.php avec les nouvelles colonnes v3.
 * =====================================================================
 */

declare(strict_types=1);

class Recipe
{
    /* ------------------------------------------------------------------ */
    /*  Constantes métier                                                   */
    /* ------------------------------------------------------------------ */

    public const CATEGORIES = [
        'salade', 'bowl', 'smoothie', 'soupe', 'plat',
        'dessert', 'petit-dejeuner', 'snack', 'other',
    ];

    public const DIFFICULTES = ['facile', 'moyen', 'difficile'];

    /** Tags filtrables (correspondent aux filtres UI) */
    public const TAGS_DISPONIBLES = [
        'healthy', 'vegan', 'low-carb', 'high-protein', 'rapide', 'budget',
    ];

    /* ------------------------------------------------------------------ */
    /*  Attributs privés                                                    */
    /* ------------------------------------------------------------------ */

    private ?int    $id;
    private string  $nom;
    private ?string $description;
    private string  $categorie;
    private string  $difficulte;
    private array   $tags;           // tableau interne, stocké en CSV en BD
    private int     $dureeMinutes;
    private int     $calories;
    private int     $nbVues;
    private ?string $imageUrl;
    private ?string $dateCreation;

    /* ------------------------------------------------------------------ */
    /*  Constructeur                                                        */
    /* ------------------------------------------------------------------ */

    public function __construct(
        string  $nom          = '',
        ?string $description  = null,
        string  $categorie    = 'other',
        string  $difficulte   = 'moyen',
        array   $tags         = [],
        int     $dureeMinutes = 0,
        int     $calories     = 0,
        int     $nbVues       = 0,
        ?string $imageUrl     = null,
        ?string $dateCreation = null,
        ?int    $id           = null
    ) {
        $this->id            = $id;
        $this->nom           = $nom;
        $this->description   = $description;
        $this->categorie     = $categorie;
        $this->difficulte    = $difficulte;
        $this->tags          = $tags;
        $this->dureeMinutes  = $dureeMinutes;
        $this->calories      = $calories;
        $this->nbVues        = $nbVues;
        $this->imageUrl      = $imageUrl;
        $this->dateCreation  = $dateCreation;
    }

    /* ------------------------------------------------------------------ */
    /*  Factory — crée un Recipe depuis une ligne PDO fetchAssoc            */
    /* ------------------------------------------------------------------ */

    public static function fromRow(array $row): self
    {
        return new self(
            nom:          $row['nom']           ?? '',
            description:  $row['description']   ?? null,
            categorie:    $row['categorie']      ?? 'other',
            difficulte:   $row['difficulte']     ?? 'moyen',
            tags:         self::parseTags($row['tags'] ?? ''),
            dureeMinutes: (int) ($row['duree_minutes'] ?? 0),
            calories:     (int) ($row['calories']      ?? 0),
            nbVues:       (int) ($row['nb_vues']        ?? 0),
            imageUrl:     $row['image_url']      ?? null,
            dateCreation: $row['date_creation']  ?? null,
            id:           isset($row['id_recette']) ? (int) $row['id_recette'] : null,
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Getters                                                             */
    /* ------------------------------------------------------------------ */

    public function getId(): ?int           { return $this->id; }
    public function getNom(): string        { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getCategorie(): string  { return $this->categorie; }
    public function getDifficulte(): string { return $this->difficulte; }
    public function getTags(): array        { return $this->tags; }
    public function getDureeMinutes(): int  { return $this->dureeMinutes; }
    public function getCalories(): int      { return $this->calories; }
    public function getNbVues(): int        { return $this->nbVues; }
    public function getImageUrl(): ?string  { return $this->imageUrl; }
    public function getDateCreation(): ?string { return $this->dateCreation; }

    /** Renvoie les tags en CSV pour l'insertion SQL */
    public function getTagsCsv(): string
    {
        return implode(',', $this->tags);
    }

    /* ------------------------------------------------------------------ */
    /*  Setters                                                             */
    /* ------------------------------------------------------------------ */

    public function setId(?int $id): void              { $this->id = $id; }
    public function setNom(string $nom): void          { $this->nom = $nom; }
    public function setDescription(?string $d): void   { $this->description = $d; }
    public function setCategorie(string $c): void      { $this->categorie = $c; }
    public function setDifficulte(string $d): void     { $this->difficulte = $d; }
    public function setDureeMinutes(int $d): void      { $this->dureeMinutes = $d; }
    public function setCalories(int $c): void          { $this->calories = $c; }
    public function setNbVues(int $n): void            { $this->nbVues = $n; }
    public function setImageUrl(?string $u): void      { $this->imageUrl = $u; }

    /**
     * Définit les tags depuis un tableau ou une chaîne CSV.
     * @param array|string $tags
     */
    public function setTags(array|string $tags): void
    {
        $this->tags = is_array($tags) ? $tags : self::parseTags($tags);
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers métier                                                      */
    /* ------------------------------------------------------------------ */

    /** Vrai si la recette porte le tag donné */
    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags, true);
    }

    /** Libellé de difficulté avec emoji */
    public function difficulteLabel(): string
    {
        return match($this->difficulte) {
            'facile'    => '🟢 Facile',
            'difficile' => '🔴 Difficile',
            default     => '🟡 Moyen',
        };
    }

    /** Libellé de durée formaté */
    public function dureeLabel(): string
    {
        if ($this->dureeMinutes < 60) {
            return "{$this->dureeMinutes} min";
        }
        $h = intdiv($this->dureeMinutes, 60);
        $m = $this->dureeMinutes % 60;
        return $m > 0 ? sprintf('%dh%02d', $h, $m) : "{$h}h";
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers statiques                                                   */
    /* ------------------------------------------------------------------ */

    /** Parse une chaîne CSV de tags en tableau propre */
    private static function parseTags(string $csv): array
    {
        if (trim($csv) === '') return [];
        return array_values(array_filter(
            array_map('trim', explode(',', $csv))
        ));
    }
}
