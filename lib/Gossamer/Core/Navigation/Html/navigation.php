

<?php $uriPrefix = '..';
?>
<div class="tr-pagination tr-section text-center">
    <ul class="pagination">
        <?php if(is_array($pagination['prev'])) {?>
            <li class="float-left"><a href="../<?php echo $pagination['prev']['offset'];?>/<?php echo $pagination['prev']['limit'];?>">Prev</a></li>
        <?php }

        $counter = 1;
        foreach($pagination['index'] as $page) {
            $class = $page['current'] == 'current' ? 'class="active"' : '';
            ?>
            <li <?php echo $class;?>><a href="../<?php echo $page['offset'];?>/<?php echo $page['limit'];?>"><?php echo $counter++;?></a></li>

        <?php }
        if(is_array($pagination['next'])) {?>
            <li class="float-right"><a href="../<?php echo $pagination['next']['offset'];?>/<?php echo $pagination['next']['limit'];?>">Next</a></li>
        <?php }?>
    </ul>
</div>