<?php

namespace App\BLL;

use App\Repository\CategoriaRepository;

class CategoriaBLL
{
    private CategoriaRepository $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function getAll(): array
    {
        return $this->categoriaRepository->findAll();
    }
}
