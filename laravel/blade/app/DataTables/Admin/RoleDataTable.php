<?php

namespace App\DataTables\Admin;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    /**
     * @param QueryBuilder<Role> $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('permissions_count', fn (Role $role) => $role->permissions_count)
            ->addColumn('action', function (Role $role) {
                $edit = view('components.admin.table.edit-button', [
                    'href'       => route('admin.roles.edit', $role),
                    'permission' => 'roles.edit',
                ])->render();

                $delete = view('components.admin.table.delete-button', [
                    'action'     => route('admin.roles.destroy', $role),
                    'name'       => $role->name,
                    'permission' => 'roles.delete',
                ])->render();

                return '<div class="hstack gap-2 justify-content-end">' . $edit . $delete . '</div>';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * @return QueryBuilder<Role>
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->withCount('permissions');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('role-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->selectStyleSingle()
            ->parameters([
                'dom' => '<"d-flex align-items-center justify-content-between px-3 pt-3"lf>rt<"d-flex align-items-center justify-content-between px-3 pb-3"ip>',
                'language' => [
                    'search'            => '',
                    'searchPlaceholder' => 'Cari...',
                    'lengthMenu'        => '_MENU_',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Nama Role'),
            Column::make('guard_name')->title('Guard'),
            Column::computed('permissions_count')
                ->title('Jumlah Permission')
                ->exportable(false)
                ->printable(false)
                ->width(150),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Role_' . date('YmdHis');
    }
}
