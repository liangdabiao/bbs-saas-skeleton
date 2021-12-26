<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Category;
//use App\Models\Tenant;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Admin;

class CategoryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Category(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('name')->copyable();
            $grid->column('description', '描述');
            $grid->column('post_count'); 

            //$grid->disableCreateButton();
            //$grid->disableEditButton();
            if (!Admin::user()->can('permissions')) {
                $grid->disableDeleteButton();
            }

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
                //$filter->equal('tenant_id', '关联租户')->select(Tenant::pluck('name', 'id'));
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param  mixed  $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Category(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description', '描述');
            $show->field('post_count'); 

            $show->disableEditButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Category(), function (Form $form) {
            $form->display('id');
            $form->text('name')
                ->rules(
                    'required|unique:categories,name,'.$form->getKey(),
                    [
                        'required' => '请填写类别名称',
                        'unique' => '类别名称已经存在'
                    ]
                )
                ->required();
            
            $form->text('description');
            //$form->text('post_count');

        });
    }
}
