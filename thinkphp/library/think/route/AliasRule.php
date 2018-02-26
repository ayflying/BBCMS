<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\route;

use think\Container;
use think\Route;

class AliasRule extends Domain
{
    protected $route;

    /**
     * 架构函数
     * @access public
     * @param  Route             $router 路由实例
     * @param  RuleGroup         $parent 上级对象
     * @param  string|array      $rule 路由规则
     * @param  string|\Closure   $route 路由地址
     * @param  array             $option 路由参数
     */
    public function __construct(Route $router, RuleGroup $parent, $name, $route, $option = [])
    {
        $this->router = $router;
        $this->parent = $parent;
        $this->name   = $name;
        $this->route  = $route;
        $this->option = $option;
    }

    /**
     * 检测域名路由
     * @access public
     * @param  Request      $request  请求对象
     * @param  string       $url      访问地址
     * @param  string       $depr     路径分隔符
     * @return Dispatch|false
     */
    public function check($request, $url, $depr = '/')
    {
        if ($dispatch = $this->checkCrossDomain($request)) {
            // 允许跨域
            return $dispatch;
        }

        // 检查参数有效性
        if (!$this->checkOption($this->option, $request)) {
            return false;
        }

        list($action, $bind) = array_pad(explode('|', $url, 2), 2, '');

        if (isset($this->option['allow']) && !in_array($action, $this->option['allow'])) {
            // 允许操作
            return false;
        } elseif (isset($this->option['except']) && in_array($action, $this->option['except'])) {
            // 排除操作
            return false;
        }

        if (isset($this->option['method'][$action])) {
            $this->option['method'] = $this->option['method'][$action];
        }

        // 指定Response响应数据
        if (!empty($this->option['response'])) {
            Container::get('hook')->add('response_send', $this->option['response']);
        }

        // 开启请求缓存
        if (isset($this->option['cache']) && $request->isGet()) {
            $this->parseRequestCache($request, $this->option['cache']);
        }

        if ($this->parent) {
            // 合并分组参数
            $this->mergeGroupOptions();
        }

        if (!empty($this->option['append'])) {
            $request->route($this->option['append']);
        }

        if (0 === strpos($this->route, '\\')) {
            // 路由到类
            return $this->bindToClass($bind, substr($this->route, 1), $depr);
        } elseif (0 === strpos($this->route, '@')) {
            // 路由到控制器类
            return $this->bindToController($bind, substr($this->route, 1), $depr);
        } else {
            // 路由到模块/控制器
            return $this->bindToModule($bind, $this->route, $depr);
        }
    }

    /**
     * 设置允许的操作方法
     * @access public
     * @param  array      $action  操作方法
     * @return $this
     */
    public function allow($action = [])
    {
        return $this->option('allow', $action);
    }

    /**
     * 设置排除的操作方法
     * @access public
     * @param  array      $action  操作方法
     * @return $this
     */
    public function except($action = [])
    {
        return $this->option('except', $action);
    }

    /**
     * 获取当前的路由
     * @access public
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }
}
