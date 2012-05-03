<script type="text/javascript">
    $(document).ready(function(){

        $("table")
            .tablesorter({widthFixed: true, widgets: ['zebra']})
            .tablesorterPager({container: $("#pager")});
    })
</script>

<h2><?php echo __('Manage files') ?></h2>

<!-- TODO : find a jquery plugin to order and paginate the user list -->

<table id="file_list" class="data tablesorter">
  <thead>
  <tr>
    <th><?php echo __('Name') ?></th>
    <th><?php echo __('Author') ?></th>
    <th><?php echo __('Availability') ?></th>
    <th><?php echo __('Size') ?></th>
    <th><?php echo __('DL count') ?></th>
    <th><?php echo __('Actions') ?></th>
  </tr>
  </thead>

 <tbody>
<?php foreach ($files as $file): ?>
  <tr>
    <td><a href="<?php echo $file->getDownloadUrl () ?>"><?php echo h($file->file_name) ?></a></td>
    <td>
      <a href="<?php echo url_for ('/admin/users/'.$file->getUploader ()->id) ?>">
        <?php echo h($file->getUploader ()) ?> (<?php echo h($file->getUploader()->username) ?>)
      </a>
    </td>
    <td><?php echo __r('from %from% to %to%', array (
      'from' => ($file->getAvailableFrom  ()->get (Zend_Date::MONTH) ==
                 $file->getAvailableUntil ()->get (Zend_Date::MONTH)) ?
                 $file->getAvailableFrom ()->toString ('d') : $file->getAvailableFrom ()->toString ('d MMMM'),
      'to' =>  '<b>'.$file->getAvailableUntil ()->toString ('d MMMM').'</b>')) // FIXME I18N ?>
    </td>
    <td><?php echo $file->getReadableFileSize () ?></td>
    <td><?php echo (int) $file->download_count ?></td>
    <td><a href="<?php echo $file->getDownloadUrl () . '/delete' ?>"><?php echo __('Delete') ?></a></td>
<?php endforeach ?>
    </tbody>
</table>

<div id="pager" class="pager">
    <form>
        <img src="../resources/images/first.png" class="first"/>
        <img src="../resources/images/prev.png" class="prev"/>
        <input type="text" disabled="disabled" class="pagedisplay"/>
        <img src="../resources/images/next.png" class="next"/>
        <img src="../resources/images/last.png" class="last"/>
        <select class="pagesize">
            <option selected="selected"  value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option  value="40">40</option>
        </select>
    </form>
</div>