<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Topic;
use App\Models\User;
use App\Models\Category;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Admin;

class TopicController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Topic(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            //$grid->column('title')->copyable();
             
            $grid->column('title')->display(function ($title) {
                return '<div style="max-width:260px">' . model_link($title,$this ) . '</div>';
            });

            $grid->column('user')->display(function ($user) {
                $avatar = $this->user->avatar;
                $value = empty($avatar) ? 'N/A' : '<img src="'.$avatar.'" style="height:22px;width:22px"> ' . $this->user->name;
                return model_link($value, $this->user);

                //return '<div style="max-width:260px">' . model_link($title,$this ) . '</div>';
            });

            $grid->column('category')->display(function ($category) {
                return model_admin_link($this->category->name, $this->category);
            });

            $grid->column('reply_count');
             
 

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('title');
                $filter->equal('user_id', '关联用户')->select(User::pluck('name', 'id'));
                $filter->equal('category_id', '关联品类')->select(Category::pluck('name', 'id'));
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
        $model = Topic::with(['user','category']);
        return Show::make($id, $model, function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('body', '描述');
            $show->field('user.name', 'user.name');
            $show->field('category.name', 'category.name');
             
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
        return Form::make(new Topic(), function (Form $form) {
            $form->display('id');
            $form->text('title')
                ->rules(
                    'required|unique:topics,title,'.$form->getKey(),
                    [
                        'required' => '请填写title名称',
                        'unique' => 'title名称已经存在'
                    ]
                )
                ->required();
            $user_select = User::pluck('name','id')->all(); 
            $form->select('user_id')->options($user_select);

            $category_select = Category::pluck('name','id')->all(); 
            $form->select('category_id')->options($category_select);

            $form->text('reply_count');
            $form->text('view_count'); 

        });
    }
}
