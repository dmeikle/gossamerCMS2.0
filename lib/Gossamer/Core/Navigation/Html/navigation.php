

<?php $uriPrefix = '..';
?>
<div>
  <!--  <select id="resultsPerPage">
        <option>10</option>
        <option>25</option>
        <option>50</option>
        <option>100</option>
    </select>
    -->
    <ul class="pagination">
        <?php $firstPagination = current($pagination); ?>
        <?php $lastPagination = end($pagination); ?>
        <li><a href="<?php echo $uriPrefix; ?>/<?php echo $firstPagination['offset']; ?>/<?php echo $firstPagination['limit']; ?>" class="pagination <?php echo $firstPagination['current']; ?>" >&laquo;</a></li>
        <?php foreach ($pagination as $index => $page) {
          ?>
            <li><a href="<?php echo $uriPrefix; ?>/<?php echo $page['offset']; ?>/<?php echo $page['limit']; ?>" class="pagination <?php echo $page['current']; ?>"  ><?php echo $index + 1; ?></a></li>
        <?php } ?>
        <li><a href="<?php echo $uriPrefix;?>/<?php echo $lastPagination['offset']; ?>/<?php echo $lastPagination['limit']; ?>" class="pagination <?php echo $lastPagination['current']; ?>"   >&raquo;</a></li>
    </ul>
</div>