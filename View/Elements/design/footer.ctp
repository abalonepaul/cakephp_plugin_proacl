<?php
if (isset($this->Js)) {
    echo $this->Js->writeBuffer();
}
?>
</div>

    <?php echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');?>
    <?php echo $this->Html->scriptBlock(" window.jQuery || document.write('<script type=\"text/javascript\" src=\"/js/libs/jquery-1.10.2.min.js\"><\/script>'); ");?>
    
    <!-- Latest compiled and minified JavaScript -->
    <?php echo $this->Html->script('//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js');?>
