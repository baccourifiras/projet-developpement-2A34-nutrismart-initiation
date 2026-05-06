<?php
/**
 * ============================================================
 *  NutriSmart - Validator
 *  /core/Validator.php
 *
 *  Validation backend simple à base de règles déclaratives.
 *  Exemple :
 *    $v = new Validator($_POST);
 *    $v->required('nom')->max('nom', 150)
 *      ->required('duree')->numeric('duree')->min('duree', 1);
 *    if (!$v->ok()) { ... $v->errors() ... }
 * ============================================================
 */

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function val(string $field)
    {
        return $this->data[$field] ?? null;
    }

    private function addError(string $field, string $message): void
    {
        // On ne garde que la 1ère erreur par champ pour rester lisible
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    /** Champ obligatoire (string non vide ou tableau non vide). */
    public function required(string $field, ?string $msg = null): self
    {
        $v = $this->val($field);
        $empty = ($v === null)
              || (is_string($v) && trim($v) === '')
              || (is_array($v)  && count($v) === 0);
        if ($empty) {
            $this->addError($field, $msg ?? "Le champ « $field » est obligatoire.");
        }
        return $this;
    }

    /** Longueur maximale (string). */
    public function max(string $field, int $max, ?string $msg = null): self
    {
        $v = $this->val($field);
        if (is_string($v) && mb_strlen($v) > $max) {
            $this->addError($field, $msg ?? "Le champ « $field » ne doit pas dépasser $max caractères.");
        }
        return $this;
    }

    /** Longueur minimale (string). */
    public function minLen(string $field, int $min, ?string $msg = null): self
    {
        $v = $this->val($field);
        if (is_string($v) && mb_strlen(trim($v)) < $min) {
            $this->addError($field, $msg ?? "Le champ « $field » doit contenir au moins $min caractères.");
        }
        return $this;
    }

    /** Doit être numérique (entier ou float). */
    public function numeric(string $field, ?string $msg = null): self
    {
        $v = $this->val($field);
        if ($v !== null && $v !== '' && !is_numeric($v)) {
            $this->addError($field, $msg ?? "Le champ « $field » doit être un nombre.");
        }
        return $this;
    }

    /** Valeur numérique >= $min. */
    public function min(string $field, float $min, ?string $msg = null): self
    {
        $v = $this->val($field);
        if (is_numeric($v) && (float)$v < $min) {
            $this->addError($field, $msg ?? "Le champ « $field » doit être >= $min.");
        }
        return $this;
    }

    /** Valeur dans une liste. */
    public function in(string $field, array $allowed, ?string $msg = null): self
    {
        $v = $this->val($field);
        if ($v !== null && $v !== '' && !in_array($v, $allowed, true)) {
            $this->addError($field, $msg ?? "Le champ « $field » a une valeur non autorisée.");
        }
        return $this;
    }

    /** Image uploadée valide (jpg/png/webp/gif), max $maxKo Ko. */
    public function image(string $field, int $maxKo = 4096, ?string $msg = null): self
    {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $this; // pas d'image envoyée = OK (champ optionnel)
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, $msg ?? "Erreur d'upload de l'image (code {$file['error']}).");
            return $this;
        }
        if ($file['size'] > $maxKo * 1024) {
            $this->addError($field, "L'image dépasse la taille maximale ({$maxKo} Ko).");
            return $this;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowed, true)) {
            $this->addError($field, "Format d'image non supporté (jpg, png, webp ou gif uniquement).");
        }
        return $this;
    }

    public function ok(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
