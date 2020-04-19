<?php
if (!defined('__TYPECHO_ADMIN__')) exit;

include 'header.php';
include 'menu.php';

Typecho_Widget::widget('Aphorisms_Widget_Admin')->to($aphorisms);
?>

<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
               <div class="col-mb-12 typecho-list">
                <div class="typecho-list-operate clearfix">
                    <form method="get">
                        <div class="operate">
                            <label><i class="sr-only"><?php _e('全选'); ?></i><input type="checkbox" class="typecho-table-select-all" /></label>
                            <div class="btn-group btn-drop">
                            <button class="btn dropdown-toggle btn-s" type="button"><i class="sr-only"><?php _e('操作'); ?></i><?php _e('选中项'); ?> <i class="i-caret-down"></i></button>
                            <ul class="dropdown-menu">
                                <li><a lang="<?php _e('你确认要删除这些名言警句吗?'); ?>" href="<?php $security->index('/action/aphorisms-edit?do=delete'); ?>"><?php _e('删除'); ?></a></li>
                            </ul>
                            </div>
                        </div>
                    </form>
                </div><!-- end .typecho-list-operate -->

                <form method="post" name="manage_aphorisms" class="operate-form">
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table">
                        <colgroup>
                            <col width="3%"/>
                            <col width="67%"/>
                            <col width="17%"/>
                            <col width="13%"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th> </th>
                                <th><?php _e('引句'); ?></th>
                                <th><?php _e('分类'); ?></th>
                                <th><?php _e('操作'); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if($aphorisms->have()): ?>
                        <?php while($aphorisms->next()): ?>
                        <tr id="<?php $aphorisms->theId(); ?>" data-aphorism="<?php
                        $aphorism = array(
                            'quotation' => $aphorisms->quotation,
                            'reference' => $aphorisms->reference,
                            'referenceUrl' => $aphorisms->referenceUrl,
                            'text' => $aphorisms->text,
                            'sort' => $aphorisms->sort
                        );

                        echo htmlspecialchars(Json::encode($aphorism));
                        ?>">
                            <td valign="top">
                                <input type="checkbox" value="<?php $aphorisms->aid(); ?>" name="aid[]"/>
                            </td>
                            <td valign="top">
                                <div>
                                    <p><strong class="aphorism-quotation"><?php $aphorisms->quotation(); ?></strong></p>
                                    <p><?php _e('出处'); ?>:&nbsp;<span class="aphorism-reference"><?php $aphorisms->reference(true); ?></span></p>
                                </div>
                                <div class="aphorism-text"><?php $aphorisms->text(); ?></div>
                            </td>
                            <td valign="top">
                                <p class="aphorism-sort"><?php $aphorisms->sort(); ?></p>
                            </td>
                            <td valign="top">
                                <div class="comment-action">
                                    <a href="#<?php $aphorisms->theId(); ?>" rel="<?php $security->index('/action/aphorisms-edit?do=edit&aid=' . $aphorisms->aid); ?>" class="operate-edit"><?php _e('编辑'); ?></a>
                                    <a lang="<?php _e('你确认要删除 &quot;%s&quot; 的 &quot;%s&quot; 名言警句吗?', htmlspecialchars($aphorisms->reference), htmlspecialchars($aphorisms->quotation)); ?>" href="<?php $security->index('/action/aphorisms-edit?do=delete&aid=' . $aphorisms->aid); ?>" class="operate-delete"><?php _e('删除'); ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4"><h6 class="typecho-list-table-title"><?php _e('未找到数据') ?></h6></td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table><!-- end .typecho-list-table -->
                </div><!-- end .typecho-table-wrap -->
                </form><!-- end .operate-form -->

                <div class="typecho-list-operate clearfix">
                    <form method="get">
                        <?php if($aphorisms->have()): ?>
                        <ul class="typecho-pager">
                            <?php $aphorisms->pageNav(); ?>
                        </ul>
                        <?php endif; ?>
                    </form>
                </div><!-- end .typecho-list-operate -->
            </div><!-- end .typecho-list -->
        </div><!-- end .typecho-page-main -->    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
?>
<script type="text/javascript">
$(document).ready(function () {
    // 记住滚动条
    function rememberScroll () {
        $(window).bind('beforeunload', function () {
            $.cookie('__typecho_comments_scroll', $('body').scrollTop());
        });
    }

    // 自动滚动
    (function () {
        var scroll = $.cookie('__typecho_comments_scroll');

        if (scroll) {
            $.cookie('__typecho_comments_scroll', null);
            $('html, body').scrollTop(scroll);
        }
    })();

    $('.operate-delete').click(function () {
        var t = $(this), href = t.attr('href'), tr = t.parents('tr');

        if (confirm(t.attr('lang'))) {
            tr.fadeOut(function () {
                rememberScroll();
                window.location.href = href;
            });
        }

        return false;
    });

    $('.operate-edit').click(function () {
        var tr = $(this).parents('tr'), t = $(this), id = tr.attr('id'), aphorism = tr.data('aphorism');
        tr.hide();

        var edit = $('<tr class="comment-edit"><td> </td>'
                        + '<td valign="top"><form method="post" action="'
                        + t.attr('rel') + '" class="aphorism-edit-content">'
                        + '<p><label for="' + id + '-quotation"><?php _e('引句'); ?></label><input class="text-s w-100" id="'
                        + id + '-quotation" name="quotation" type="text" required></p>'
                        + '<p><label for="' + id + '-text"><?php _e('详情'); ?></label>'
                        + '<textarea name="text" id="' + id + '-text" rows="10" class="w-100 mono"></textarea></p></form></td>'
                        + '<td colspan="2" valign="top"><p><?php _e('完善详细信息'); ?></p>'
                        + '<form method="post" action="' + t.attr('rel') + '" class="aphorism-edit-info">'
                        + '<p><label for="' + id + '-reference"><?php _e('出处'); ?></label>'
                        + '<input class="text-s w-100" type="text" name="reference" id="' + id + '-reference" required></p>'
                        + '<p><label for="' + id + '-referenceUrl"><?php _e('出处地址'); ?></label>'
                        + '<input class="text-s w-100" type="text" name="referenceUrl" id="' + id + '-referenceUrl"></p>'
                        + '<p><label for="' + id + '-sort"><?php _e('分类'); ?></label>'
                        + '<input class="text-s w-100" type="text" name="sort" id="' + id + '-sort"></p>'
                        + '<p><button type="submit" class="btn btn-s primary"><?php _e('提交'); ?></button> '
                        + '<button type="button" class="btn btn-s cancel"><?php _e('取消'); ?></button></p></form></td></tr>')
                        .data('id', id).data('aphorism', aphorism).insertAfter(tr);

        $('input[name=quotation]', edit).val(aphorism.quotation).focus();
        $('textarea[name=text]', edit).val(aphorism.text);
        $('input[name=reference]', edit).val(aphorism.reference);
        $('input[name=referenceUrl]', edit).val(aphorism.referenceUrl);
        $('input[name=sort]', edit).val(aphorism.sort);

        $('.cancel', edit).click(function () {
            var tr = $(this).parents('tr');

            $('#' + tr.data('id')).show();
            tr.remove();
            t.focus();
        });

        $('form', edit).submit(function () {
            var f = $(this), tr = f.parents('tr'),
                oldTr = $('#' + tr.data('id')),
                aphorism = oldTr.data('aphorism');

            $('form', tr).each(function () {
                var items  = $(this).serializeArray();

                for (var i = 0; i < items.length; i ++) {
                    var item = items[i];
                    aphorism[item.name] = item.value;
                }
            });

            $('.aphorism-quotation', oldTr).html(aphorism.quotation).effect('highlight');
            $('.aphorism-reference', oldTr).html(aphorism.referenceUrl ? '<a href="' + aphorism.referenceUrl + '">' + aphorism.reference + '</a>' : aphorism.reference);
            $('.aphorism-text', oldTr).html(aphorism.text);
            $('.aphorism-sort', oldTr).html(aphorism.sort);
            oldTr.data('aphorism', aphorism);

            $.post(f.attr('action'), aphorism, function (o) {
            }, 'json');

            oldTr.show();
            tr.remove();

            t.focus();
            return false;
        });

        return false;
    });
});
</script>
<?php include 'footer.php'; ?>
