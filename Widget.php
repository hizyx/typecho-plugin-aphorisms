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
        /** 构建插入结构 */
        $insertStruct = array(
            'quotation' => $aphor['quotation'],
            'reference' => !isset($aphor['reference']) || strlen($aphor['reference']) === 0 ? _t('匿名') : $aphor['reference'],
            'referenceUrl' => !isset($aphor['referenceUrl']) || strlen($aphor['referenceUrl']) === 0 ? NULL : $aphor['referenceUrl'],
            'text' => !isset($aphor['text']) || strlen($aphor['text']) === 0 ? NULL : $aphor['text'],
            'sort' => !isset($aphor['sort']) || strlen($aphor['sort']) === 0 ? NULL : $aphor['sort'],
        );

        if (!empty($aphor['aid'])) {
            $insertStruct['aid'] = $aphor['aid'];
        }

        $insertId = $this->db->query($this->db->insert('table.aphorisms')->rows($insertStruct));
        return $insertId;
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
        $updateCondition = clone $condition;

        /** 构建插入结构 */
        $preUpdateStruct = array(
            'quotation' => $rows['quotation'],
            'reference' => $rows['reference'],
            'referenceUrl' => !isset($rows['referenceUrl']) || strlen($rows['referenceUrl']) === 0 ? NULL : $rows['referenceUrl'],
            'text' => !isset($rows['text']) || strlen($rows['text']) === 0 ? NULL : $rows['text'],
            'sort' => !isset($rows['sort']) || strlen($rows['sort']) === 0 ? NULL : $rows['sort'],
        );

        $updateStruct = array();
        foreach ($rows as $key => $val) {
            if (array_key_exists($key, $preUpdateStruct)) {
                $updateStruct[$key] = $preUpdateStruct[$key];
            }
        }

        /** 更新名言警句数据 */
        $updateRows = $this->db->query($updateCondition->update('table.aphorisms')->rows($updateStruct));

        return $updateRows;
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
