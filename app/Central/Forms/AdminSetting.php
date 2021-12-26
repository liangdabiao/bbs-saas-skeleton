<?php

namespace App\Central\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Form;
use Illuminate\Support\Arr;

class AdminSetting extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单请求.
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        foreach (Arr::dot($input) as $k => $v) {
            admin_setting([$k => $v]);
        }

        return $this->response()->success('设置成功');
    }

    /**
     * 构建表单.
     */
    public function form()
    {
        $this->confirm('确认提交设置吗?', '请确认您所填写的配置项数据都是正确的2');

        $this->tab('基础配置2', function () {
            $alert = new Alert('系统配置测试3'); 

            //$this->html($alert->warning());

            $this->text('name')->required()->help('网站名称');
            $this->text('logo')->required()->help('logo设置');
            

            $this->checkbox('layout.body_class', '菜单布局')
                ->options([
                    'horizontal_menu' => '水平 (Horizontal)',
                    'sidebar-separate' => 'sidebar-separate',
                ])
                ->help('切换菜单布局');
    //        $this->switch('https', '启用HTTPS');
            $this->switch('helpers.enable', '开发工具2');


        });
    }

    /**
     * 设置接口保存成功后的回调JS代码.
     *
     * 1.2秒后刷新整个页面.
     *
     * @return string|void
     */
    public function savedScript()
    {
        return <<<'JS'
    if (data.status) {
        setTimeout(function () {
          location.reload()
        }, 1200);
    }
JS;
    }

    /**
     * 返回表单数据.
     *
     * @return array
     */
    public function default()
    {
        return admin_setting()->toArray();
    }
}
