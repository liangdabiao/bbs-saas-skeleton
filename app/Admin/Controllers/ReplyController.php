<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Reply;
use App\Models\User;
use App\Models\Category;
use App\Models\Topic;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Admin;

class ReplyController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Reply(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            //$grid->column('title')->copyable();
             
            $grid->column('content')->display(function ($content) {
                return '<div style="max-width:220px">' . $content . '</div>';
            });

            $grid->column('user')->display(function ($user) {
                if(!$user){
                    return $this->user_id;
                }
                $avatar = $this->user->avatar;
                $value = empty($avatar) ? 'N/A' : '<img src="'.$avatar.'" style="height:22px;width:22px"> ' . $this->user->name;
                return model_link($value, $this->user);
            });

            $grid->column('topic')->display(function ($topic) {
                if(!$topic){
                    return $this->topic_id;
                }
                return '<div style="max-width:260px">' . model_admin_link($this->topic->title, $this->topic) . '</div>';
            });

          

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('content');
                $filter->equal('user_id', '关联用户')->select(User::pluck('name', 'id'));
                $filter->equal('topic_id', '关联topic')->select(Category::pluck('name', 'id'));
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
        $model = Reply::with(['user','topic']);
        return Show::make($id, $model, function (Show $show) {
            $show->field('id'); 
            $show->field('content', '描述');
            $show->field('user.name', 'user.name');
            $show->field('topic.title', 'topic.title');
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
        return Form::make(new Reply(), function (Form $form) {
            $form->display('id');
            $form->text('content');
                
            $user_select = User::pluck('name','id')->all(); 
            $form->select('user_id')->options($user_select);

            $topic_select = Topic::pluck('title','id')->all(); 
            $form->select('topic_id')->options($topic_select);
 

        });
    }
}
