<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Link;
//use App\Models\Tenant;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Admin;

class LinkController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Link(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('title')->copyable();
            $grid->column('link', 'link');
            $grid->column('post_count'); 

            //$grid->disableCreateButton();
            //$grid->disableEditButton();
             

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('title');
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
        return Show::make($id, new Link(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('link', 'link'); 

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
        return Form::make(new Link(), function (Form $form) {
            $form->display('id');
            $form->text('title')
                ->rules(
                    'required|unique:links,title,'.$form->getKey(),
                    [
                        'required' => '请填写title名称',
                        'unique' => 'title名称已经存在'
                    ]
                )
                ->required();
            
            $form->text('link');
            //$form->text('post_count');

        });
    }
}
