<?php declare(strict_types=1);

namespace App\Repositories\Category;

use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * カテゴリーを一覧取得
     *
     * @return Collection
     */
    public function get(): Collection;
}
