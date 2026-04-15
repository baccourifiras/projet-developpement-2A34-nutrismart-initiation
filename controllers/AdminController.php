<?php
require_once __DIR__ . '/../models/Stock.php';
require_once __DIR__ . '/../models/ListeCourses.php';

class AdminController {
    private Stock        $stockModel;
    private ListeCourses $listeModel;

    public function __construct() {
        $this->stockModel = new Stock();
        $this->listeModel = new ListeCourses();
    }

    public function dashboard(): void {
        $totalStocks     = $this->stockModel->count();
        $totalListes     = $this->listeModel->count();
        $tauxGaspillage  = $this->stockModel->getTauxGaspillage();
        $expirentBientot = $this->stockModel->getExpiresSoon(7);
        $tousLesStocks   = $this->stockModel->getAll();
        $toutesLesListes = $this->listeModel->getAll();
        require __DIR__ . '/../views/admin/dashboard.php';
    }
}
