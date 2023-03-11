<?php declare(strict_types=1);

namespace App\Http\Services;

use App\Repositories\Category\CategoryInterface;
use Illuminate\Support\Collection;

class CommonService
{
    private CategoryInterface $category_repository;

    public function __construct(CategoryInterface $category_repository)
    {
        $this->category_repository = $category_repository;
    }

    /**
     * カテゴリー情報の取得
     *
     * @return Collection
     */
    public function getCategory(): Collection
    {
        return $this->category_repository->get();
    }
}
