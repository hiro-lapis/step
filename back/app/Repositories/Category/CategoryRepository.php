<?php declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private Category $category,
    )
    {}

    /**
     * カテゴリーを一覧取得
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->category->orderBy('sort_number')->get();
    }
}
