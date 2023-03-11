<?php declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    private Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * カテゴリーを一覧取得
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->category->get();
    }
}
