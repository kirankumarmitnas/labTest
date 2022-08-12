<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
$totalPages=$pager->getPageCount();
?>
	
<nav class="user-search-pagination" aria-label="<?= lang('Pager.pageNavigation') ?>">
	<ul class="pagination justify-content-center btn-group btn-group-sm">
		<?php if ($pager->hasPrevious()) : ?>
			<li class="page-item">
				<span class="page-link">
				<a class="text-decoration-none" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
					<span aria-hidden="true"><?= lang('Pager.first') ?>-0-</span>
				</a>
				</span>
			</li>
			<li class="page-item">
			   <span class="page-link">
				<a href="<?= $pager->getPrevious() ?>" class="text-decoration-none" aria-label="<?= lang('Pager.previous') ?>">
					<span aria-hidden="true"><?= lang('Pager.previous') ?>-0-</span>
				</a>
				</span>
			</li>
		<?php endif ?>
   
		<?php 
		if ($totalPages>1) :
		foreach ($pager->links() as $link) : ?>
			<li <?= $link['active'] ? 'class="page-item active"' : 'class="page-item" ' ?>>
			 <span class="page-link">
				<a href="<?= $link['uri'] ?>" class="<?= $link['active'] ? 'text-white' : '' ?>  text-decoration-none">
					<?= $link['title'] ?>
				</a>
				</span>
			</li>
		<?php endforeach ?>
		<?php endif ?>

		<?php if ($pager->hasNext()) : ?>
			<li class="page-item">
			    <span class="page-link">
				<a href="<?= $pager->getNext() ?>" class="text-decoration-none" aria-label="<?= lang('Pager.next') ?>">
					<span aria-hidden="true"><?= lang('Pager.next') ?></span>
				</a>
				</span>
			</li>
			<li class="page-item">
			   <span class="page-link">
				<a href="<?= $pager->getLast() ?>" class="text-decoration-none" aria-label="<?= lang('Pager.last') ?>">
					<span aria-hidden="true"><?= lang('Pager.last') ?></span>
				</a>
				</span>
			</li>
		<?php endif ?>
	</ul>
</nav>
