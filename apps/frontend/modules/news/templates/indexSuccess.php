<?php slot("title", __("Hírek")) ?>
<?php use_helper("Date") ?>
<?php include_partial("list", array("news" => $pager->getResults())) ?>

<?php if ($pager->haveToPaginate()): ?>
  <div class="pagination">
    <a href="<?php echo url_for("@news_index?page=1") ?>">&laquo;</a> 
    <a href="<?php echo url_for("@news_index?page=" . $pager->getPreviousPage()) ?>">&lsaquo;</a>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <b><?php echo $page ?></b>
      <?php else: ?>
        <a href="<?php echo url_for("@news_index?page=" . $page) ?>"><?php echo $page ?></a> 
      <?php endif ?>
    <?php endforeach ?>
    
    <a href="<?php echo url_for("@news_index?page=" . $pager->getNextPage()) ?>">&rsaquo;</a> 
    <a href="<?php echo url_for("@news_index?page=" . $pager->getLastPage()) ?>">&raquo;</a>
  </div>
<?php endif ?>
