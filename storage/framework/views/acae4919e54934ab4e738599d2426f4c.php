<?php ($level = min(6, max(1, (int) ($node['settings']['level'] ?? 2)))); ?>
<h<?php echo e($level); ?> data-node-id="<?php echo e($node['id']); ?>" style="<?php echo e($style); ?>"><?php echo e($node['content']['text'] ?? ''); ?></h<?php echo e($level); ?>>
<?php /**PATH C:\laragon\www\visual-builder\resources\views/builder/blocks/heading.blade.php ENDPATH**/ ?>