<?php

namespace App\Repositories;

use App\Models\Link;

class LinkRepository {

    public function store(array $data) {
        return Link::create($data);
    }

    public function getAll() {
        return Link::orderBy('created_at', 'desc')->get();
    }
}
