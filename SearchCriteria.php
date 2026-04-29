<?php
/**
 * =====================================================================
 *  NutriSmart v3 — Model/SearchCriteria.php
 *  Value Object immuable qui encapsule tous les paramètres d'une
 *  recherche avancée. Sert d'interface entre le Controller,
 *  le SearchManager et le RecipeRepository.
 *
 *  Utilisation :
 *    $criteria = SearchCriteria::fromRequest($_GET);
 *    $results  = $searchManager->search($criteria);
 * =====================================================================
 */

declare(strict_types=1);

class SearchCriteria
{
    /* ------------------------------------------------------------------ */
    /*  Constantes de tri valides (whitelist — évite les injections SQL)   */
    /* ------------------------------------------------------------------ */

    public const SORT_FIELDS = [
        'nom'            => 'r.nom',
        'duree'          => 'r.duree_minutes',
        'calories'       => 'r.calories',
        'date'           => 'r.date_creation',
        'difficulte'     => 'r.difficulte',
        'popularite'     => 'r.nb_vues',
    ];

    public const SORT_ORDERS = ['ASC', 'DESC'];

    /* ------------------------------------------------------------------ */
    /*  Propriétés readonly (immuabilité PHP 8.1+)                         */
    /* ------------------------------------------------------------------ */

    public readonly string  $terme;         // Recherche texte libre
    public readonly string  $categorie;     // Filtre catégorie exacte
    public readonly string  $ingredient;    // Filtre par nom d'ingrédient
    public readonly array   $tags;          // Tags actifs : ['vegan','rapide']
    public readonly ?int    $calMin;        // Plage de calories min
    public readonly ?int    $calMax;        // Plage de calories max
    public readonly ?int    $dureeMax;      // Durée max (filtre "rapide")
    public readonly string  $sortField;     // Colonne de tri (whitelist)
    public readonly string  $sortOrder;     // 'ASC' | 'DESC'
    public readonly int     $page;          // Page courante (pagination)
    public readonly int     $perPage;       // Résultats par page

    /* ------------------------------------------------------------------ */
    /*  Constructeur privé — on passe par ::fromRequest() ou ::fromArray() */
    /* ------------------------------------------------------------------ */

    private function __construct(
        string $terme,
        string $categorie,
        string $ingredient,
        array  $tags,
        ?int   $calMin,
        ?int   $calMax,
        ?int   $dureeMax,
        string $sortField,
        string $sortOrder,
        int    $page,
        int    $perPage
    ) {
        $this->terme      = $terme;
        $this->categorie  = $categorie;
        $this->ingredient = $ingredient;
        $this->tags       = $tags;
        $this->calMin     = $calMin;
        $this->calMax     = $calMax;
        $this->dureeMax   = $dureeMax;
        $this->sortField  = $sortField;
        $this->sortOrder  = $sortOrder;
        $this->page       = $page;
        $this->perPage    = $perPage;
    }

    /* ------------------------------------------------------------------ */
    /*  Factory depuis $_GET / $_POST                                       */
    /* ------------------------------------------------------------------ */

    public static function fromRequest(array $params = []): self
    {
        if (empty($params)) $params = $_GET;

        $terme      = trim(strip_tags($params['q']         ?? ''));
        $categorie  = trim(strip_tags($params['categorie'] ?? ''));
        $ingredient = trim(strip_tags($params['ingredient'] ?? ''));

        // Tags : on accepte un tableau GET ou une chaîne CSV
        $rawTags = $params['tags'] ?? [];
        if (is_string($rawTags)) {
            $rawTags = array_filter(array_map('trim', explode(',', $rawTags)));
        }
        $tags = array_values(array_intersect(
            (array) $rawTags,
            Recipe::TAGS_DISPONIBLES    // Whitelist absolue
        ));

        $calMin  = isset($params['cal_min'])  && is_numeric($params['cal_min'])
                   ? max(0, (int) $params['cal_min']) : null;
        $calMax  = isset($params['cal_max'])  && is_numeric($params['cal_max'])
                   ? max(0, (int) $params['cal_max']) : null;
        $dureeMax = isset($params['duree_max']) && is_numeric($params['duree_max'])
                   ? max(0, (int) $params['duree_max']) : null;

        if ($calMin !== null && $calMax !== null && $calMin > $calMax) {
            [$calMin, $calMax] = [$calMax, $calMin];
        }

        // Tri — whitelist stricte
        $sortField = isset(self::SORT_FIELDS[$params['sort'] ?? ''])
                     ? $params['sort']
                     : 'date';
        $sortOrder = strtoupper($params['order'] ?? 'DESC');
        if (!in_array($sortOrder, self::SORT_ORDERS, true)) $sortOrder = 'DESC';

        $page    = max(1, (int) ($params['page']     ?? 1));
        $perPage = max(5, min(50, (int) ($params['per_page'] ?? 10)));

        return new self(
            $terme, $categorie, $ingredient, $tags,
            $calMin, $calMax, $dureeMax,
            $sortField, $sortOrder,
            $page, $perPage
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                             */
    /* ------------------------------------------------------------------ */

    /** Vrai si au moins un critère de filtre est actif */
    public function hasFilters(): bool
    {
        return $this->terme !== ''
            || $this->categorie !== ''
            || $this->ingredient !== ''
            || !empty($this->tags)
            || $this->calMin !== null
            || $this->calMax !== null
            || $this->dureeMax !== null;
    }

    /** Sérialise les critères en JSON (pour search_history.filtres) */
    public function toJson(): string
    {
        return json_encode([
            'q'         => $this->terme,
            'categorie' => $this->categorie,
            'ingredient'=> $this->ingredient,
            'tags'      => $this->tags,
            'cal_min'   => $this->calMin,
            'cal_max'   => $this->calMax,
            'duree_max' => $this->dureeMax,
            'sort'      => $this->sortField,
            'order'     => $this->sortOrder,
        ], JSON_UNESCAPED_UNICODE);
    }

    /** Reconstruit la query string pour les liens de pagination */
    public function toQueryString(array $overrides = []): string
    {
        $params = array_merge([
            'q'          => $this->terme,
            'categorie'  => $this->categorie,
            'ingredient' => $this->ingredient,
            'tags'       => implode(',', $this->tags),
            'cal_min'    => $this->calMin ?? '',
            'cal_max'    => $this->calMax ?? '',
            'duree_max'  => $this->dureeMax ?? '',
            'sort'       => $this->sortField,
            'order'      => $this->sortOrder,
            'per_page'   => $this->perPage,
        ], $overrides);

        // Supprime les paramètres vides
        $params = array_filter($params, fn($v) => $v !== '' && $v !== null);

        return http_build_query($params);
    }

    /** Retourne la colonne SQL réelle pour le tri (depuis la whitelist) */
    public function sortColumn(): string
    {
        return self::SORT_FIELDS[$this->sortField] ?? 'r.date_creation';
    }
}
