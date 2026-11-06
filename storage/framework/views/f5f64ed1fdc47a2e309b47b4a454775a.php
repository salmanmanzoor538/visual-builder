<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($publishedPage->snapshot['page']['seo_title'] ?: $publishedPage->snapshot['page']['name']); ?></title>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedPage->snapshot['page']['meta_description']): ?><meta name="description" content="<?php echo e($publishedPage->snapshot['page']['meta_description']); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedPage->rendered_css): ?><style><?php echo e($publishedPage->rendered_css); ?></style><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</head>
<body><?php echo $publishedPage->rendered_html; ?></body>
</html>
<?php /**PATH C:\laragon\www\visual-builder\resources\views/published/page.blade.php ENDPATH**/ ?>