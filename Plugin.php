<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 名言警句
 * 
 * @package Aphorisms
 * @author 好奇的 01
 * @version 1.0.0
 * @link https://github.com/hizyx/typecho-plugin-aphorisms
 */
class Aphorisms_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 最近一条名言警句
     *
     * @access private
     * @var object
     */
    private static $_currentAphorism;

    /**
     * 从数据库中随机取出一条名言警句
     * 
     * @access private
     * @return object
     */
    private static function fetchRandomOne()
    {
        /** 获取数据库支持 */
        $db = Typecho_Db::get();

        $select = $db->select()->from('table.aphorisms')
->limit(1)
->order('RAND()');
        $result = $db->fetchObject($select);
        return $result;
    }

    /**
     * 创建数据表
     * 
     * @access private
     * @return string
     * @throws Typecho_Plugin_Exception
     */
    private static function initializeTable()
        {
        /** 获取数据库支持 */
        $db = Typecho_Db::get();
        $adapterName = $db->getAdapterName();
        $prefix = $db->getPrefix();

        /** 为不同适配器生成创建数据表语句 */
        if($adapterName === 'Pdo_Mysql') {
            $charset = $db->getConfig()[0]->charset;
            $engine = $db->getConfig()[0]->engine;
            $sql = str_replace(
                array('%prefix%', '%engine%', '%charset%'),
                array($prefix, $engine, $charset),
'CREATE TABLE `%prefix%aphorisms` (
  `aid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \'aphorisms 表主键\',
  `quotation` varchar(180) NOT NULL COMMENT \'引句\',
  `reference` varchar(128) NOT NULL COMMENT \'出处\',
  `referenceUrl` varchar(256) DEFAULT NULL COMMENT \'地址\',
  `text` text DEFAULT NULL COMMENT \'文本\',
  `sort` varchar(32) default NULL COMMENT \'分类\',
  PRIMARY KEY  (`aid`),
  UNIQUE KEY `quotation` (`quotation`)
) ENGINE=%engine% DEFAULT CHARSET=%charset%;');
        }
        try {
            $db->query($sql, Typecho_Db::WRITE);
            $db->query($db->insert('table.aphorisms')->rows(array('quotation' => _t('Hello World'), 'reference' => _t('Brian Kernighan'))));
            return '建立名言警句数据表成功';
        } catch (Typecho_Db_Exception $e) {
              $code = $e->getCode();
              if($code === '42S01') {
                return '检查到以前的数据表';
            } else {
                throw new Typecho_Plugin_Exception('数据表初始化失败。错误号：' . $code);
            }
        }
    }

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        $result = self::initializeTable();

        Helper::addAction('aphorisms-edit', 'Aphorisms_Widget_Edit');
        Helper::addPanel(3, 'Aphorisms/manage-aphorisms.php', _t('名言警句'), _t('管理名言警句'), 'editor');

        Typecho_Plugin::factory('admin/menu.php')->navBar = array('Aphorisms_Plugin', 'menuRender');
        Typecho_Plugin::factory('admin/header.php')->header = array('Aphorisms_Plugin', 'adminHeader');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        Helper::removeAction('aphorisms-edit');
        Helper::removePanel(3, 'Aphorisms/manage-aphorisms.php');
    }

    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form) {}

    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 输出一条名言警句（首次从数据库获取， 以后重复利用）
     * 
     * @access public
     * @pattern string
     * @return void
     */
    public static function output($pattern)
    {
        echo self::parse($pattern);
    }

    /**
     * 格式化一条名言警句（首次从数据库获取， 以后重复利用）
     * 
     * @access public
     * @pattern string
     * @return string
     */
    public static function parse($pattern)
    {
        if (!isset(Helper::options()->plugins['activated']['Aphorisms'])) {
            return _t('插件已经被禁用');
        }
        if(empty($_currentAphorism))
        {
            try {
                $_currentAphorism = self::fetchRandomOne();
            } catch (Typecho_Db_Exception $e) {
                return _t('未找到数据');
            }
        }
        if(empty($pattern) || !is_string($pattern))
        {
            $pattern = '<span>{quotation}_{reference}</span>';
        }
        return str_replace(
            array('{quotation}', '{reference}', '{referenceUrl}', '{text}'),
            array(htmlspecialchars($_currentAphorism->quotation), $_currentAphorism->reference, htmlspecialchars($_currentAphorism->referenceUrl), $_currentAphorism->text),
            $pattern);
    }

    /**
     * 后台输出名言警句
     * 
     * @access public
     * @return void
     */
    public static function menuRender()
    {
        echo self::parse('<span class="message success">{quotation}——{reference}</span>');

         Typecho_Widget::widget('Widget_User')->to($user);
        if($user->pass('editor', true)) {
            Typecho_Widget::widget('Widget_Security')->to($security);
            echo '<a class="operate-aphorism-insert" href="#aphorism-insert" rel="',$security->index('/action/aphorisms-edit?do=insert') , '" title="', _t('添加一条名言警句') ,'">', _t('　+　'), '</a>';
        }
    }

    /**
     * 后台输出 header。
     * 
     * @access public
     * @param string $header
     * @return string
     */
    public static function adminHeader($header)
    {
        $header .= '
<script type="text/javascript">
(function () {
    function bind(o, event, callback) {
        if(o.addEventListener) {
            o.addEventListener(event, callback, false);
        } else if (o.attachEvent) {
            o.attachEvent("on" + event, function (){
                callback.call(o);
            });
        }
    }

    bind(window, "load", function (){
        $(".operate-aphorism-insert").click(function () {
            var t = $(this), action = t.attr("rel");
            var result = prompt(t.attr("title"), "");
            if(result) {
                var aphor = {quotation: result};
                $.post(action, aphor, function (o) { 
                    alert("' . _t('添加成功！') . '");
                }, "json");
            }
        });
    });
})();
</script>
';
        return $header;
    }
}
