<?php

declare(strict_types=1);

namespace Orchid\Screen\Layouts;

use Illuminate\Contracts\View\Factory;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

/**
 * Class Table.
 */
abstract class Table extends Layout
{
    /**
     * @var string
     */
    protected $template = 'platform::layouts.table';

    /**
     * @var Repository
     */
    protected $query;

    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target;

    /**
     * @param Repository $repository
     *
     * @return Factory|\Illuminate\View\View
     */
    public function build(Repository $repository)
    {
        if (! $this->checkPermission($this, $repository)) {
            return;
        }

        $this->query = $repository;

        $columns = collect($this->columns())->filter(static function (TD $column) {
            return $column->isSee();
        });

        $total = collect($this->total())->filter(static function (TD $column) {
            return $column->isSee();
        });

        return view($this->template, [
            'repository'   => $repository,
            'rows'         => $repository->getContent($this->target),
            'columns'      => $columns,
            'total'        => $total,
            'iconNotFound' => $this->iconNotFound(),
            'textNotFound' => $this->textNotFound(),
            'subNotFound'  => $this->subNotFound(),
            'striped'      => $this->striped(),
            'bordered'     => $this->bordered(),
            'hoverable'    => $this->hoverable(),
            'slug'         => $this->getSlug(),
        ]);
    }

    /**
     * @return string
     */
    protected function iconNotFound(): string
    {
        return 'icon-table';
    }

    /**
     * @return string
     */
    protected function textNotFound(): string
    {
        return __('There are no records in this view');
    }

    /**
     * @return string
     */
    protected function subNotFound(): string
    {
        return '';
    }

    /**
     * Usage for zebra-striping to any table row.
     *
     * @return bool
     */
    protected function striped(): bool
    {
        return false;
    }

    /**
     * Usage for borders on all sides of the table and cells.
     *
     * @return bool
     */
    protected function bordered(): bool
    {
        return false;
    }

    /**
     * Enable a hover state on table rows.
     *
     * @return bool
     */
    protected function hoverable(): bool
    {
        return false;
    }

    /**
     * @return array
     */
    abstract protected function columns(): array;

    /**
     * @return array
     */
    protected function total(): array
    {
        return [];
    }
}
