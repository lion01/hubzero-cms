<?
$resp = $this->resp;
?>

<div id="content-header" class="full">
	<h2>Results: <?= $this->title ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="icon-back back btn" href="<?php echo JRoute::_($this->base); ?>">
			<?php echo JText::_('Back to course'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="main section">
	<p>Completed <?= JHTML::_('date', $resp->getEndTime(), 'r'); ?></p>
	<? if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>Detailed results will be available <?= JHTML::_('date', $resp->getEndTime(), 'r'); ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - strtotime(JFactory::getDate())) ?> from now). Save this link and come back then.</p>
	<? elseif ($this->dep->getResultsClosed() == 'score'): ?>
		<p>Your score will be available <?= JHTML::_('date', $resp->getEndTime(), 'r'); ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - strtotime(JFactory::getDate())) ?> from now). Save this link and come back then.</p>
	<? endif; ?>
	<? if ($this->dep->getAllowedAttempts() > 1) : ?>
		<? $attempt = $resp->getAttemptNumber(); ?>
		You are allowed <strong><?= $this->dep->getAllowedAttempts() ?></strong> attempts.
		This was your <strong><?= FormHelper::toOrdinal((int)$attempt) ?></strong> attempt.
		<? if ($this->dep->getAllowedAttempts() > $attempt) : ?>
			<a href="<?= JRoute::_($this->base . '&task=form.complete&crumb='.$this->dep->getCrumb().'&attempt='.((int)$attempt+1)) ?>">Take your <?= FormHelper::toOrdinal((int)$attempt+1) ?> attempt</a>
		<? endif; ?>
	<? endif; ?>
</div>