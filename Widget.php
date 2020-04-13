<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 名言警句组件基类
 */
class Aphorisms_Widget extends Widget_Abstract
{
    /**
     * 锚点id
     *
     * @access protected
     * @return string
     */
    protected function ___theId()
    {
        return 'aphorisms-' . $this->aid;
    }

    /**
     * 获取名言警句参考
     *
     * @access public
     * @param boolean $autoLink 是否自动输出链接
     * @return void
     */
    public function reference($autoLink = NULL)
    {
        $autoLink = (NULL === $autoLink) ? true : $autoLink;

        if ($this->referenceUrl && $autoLink) {
            echo '<a href="' , $this->referenceUrl , '">' , $this->reference , '</a>';
        } else {
            echo $this->reference;
        }
    }

    /**
     * 获取名言警句分类
     *
     * @access public
     * @return void
     */
    public function sort()
    {
        echo $this->sort ? $this->sort : _t('未分类');
    }

    /**
     * 查询方法
     *
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select()->from('table.aphorisms');
    }

    /**
     * 获得所有记录数
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select(array('COUNT(aid)' => 'num'))->from('table.aphorisms'))->num;
    }

    /**
     * 增加记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    public function insert(array $aphor)
    {
    }

    /**
     * 更新记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function update(array $rows, Typecho_Db_Query $condition)
    {
    }

    /**
     * 删除记录方法
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
    }
}
