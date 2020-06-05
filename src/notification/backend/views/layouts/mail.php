<?php
?>

<?= $this->blocks['header'] ?? '' ?>

<?php $this->beginContent($this->context->mailLayout.'.php'); ?>

<?= $content ?>

<?php $this->endContent(); ?>