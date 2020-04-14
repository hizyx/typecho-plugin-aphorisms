<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 名言警句后台数据组件
 */
class Aphorisms_Widget_Admin extends Aphorisms_Widget
{
    /**
     * 分页计算对象
     *
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;

    /**
     * 当前页
     *
     * @access private
     * @var integer
     */
    private $_currentPage;

    /**
     * 所有文章个数
     *
     * @access private
     * @var integer
     */
    private $_total = false;

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        $select = $this->select();
        $this->parameter->setDefault('pageSize=20');
        $this->_currentPage = $this->request->get('page', 1);

        $this->_countSql = clone $select;

        $select->order('table.aphorisms.aid', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->parameter->pageSize);

        $this->db->fetchAll($select, array($this, 'push'));
    }

    /**
     * 输出分页
     *
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = $this->request->makeUriByRequest('page={page}');

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render(_t('&laquo;'), _t('&raquo;'));
    }
}
