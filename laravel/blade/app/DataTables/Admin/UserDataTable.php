<?php

namespace App\DataTables\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (User $user) {
                $edit = view('components.admin.table.edit-button', [
                    'href'       => route('admin.users.edit', $user),
                    'permission' => 'users.edit',
                ])->render();

                $delete = view('components.admin.table.delete-button', [
                    'action'     => route('admin.users.destroy', $user),
                    'name'       => $user->name,
                    'permission' => 'users.delete',
                ])->render();

                return '<div class="hstack gap-2 justify-content-end">' . $edit . $delete . '</div>';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->selectStyleSingle()
            ->parameters([
                'dom' => '<"d-flex align-items-center justify-content-between px-3 pt-3"lf>rt<"d-flex align-items-center justify-content-between px-3 pb-3"ip>',
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Cari...',
                    'lengthMenu' => '_MENU_',
                ],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Nama'),
            Column::make('email')->title('Email'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-end'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
