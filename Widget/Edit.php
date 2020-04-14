<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 名言警句
 * @author 好奇的 01
 */
class Aphorisms_Widget_Edit extends Aphorisms_Widget implements Widget_Interface_Do
{
    /**
     * 删除名言警句
     *
     * @access public
     * @return void
     */
    public function deleteAphorism()
    {
        $aphorisms = $this->request->filter('int')->getArray('aid');
        $deleteRows = 0;

        foreach ($aphorisms as $aid) {
            $aphorism = $this->db->fetchRow($this->select()
            ->where('aid = ?', $aid)->limit(1), array($this, 'push'));

            if ($aphorism) {
                /** 删除名言警句 */
                $this->db->query($this->db->delete('table.aphorisms')->where('aid = ?', $aid));

                $deleteRows ++;
            }
        }

        if ($this->request->isAjax()) {
            
            if ($deleteRows > 0) {
                $this->response->throwJson(array(
                    'success'   => 1,
                    'message'   => _t('删除名言警句成功')
                ));
            } else {
                $this->response->throwJson(array(
                    'success'   => 0,
                    'message'   => _t('删除名言警句失败')
                ));
            }

        } else {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($deleteRows > 0 ? _t('名言警句已经被删除') : _t('没有名言警句被删除'),
            $deleteRows > 0 ? 'success' : 'notice');

            /** 返回原网页 */
            $this->response->goBack();
        }
    }

    /**
     * action 入口函数
     *
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('editor');
        $this->security->protect();
        $this->on($this->request->is('do=delete'))->deleteAphorism();

        $this->response->redirect($this->options->adminUrl);
    }
}
