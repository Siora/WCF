{foreach from=$queues item=queue}
	<li class="moderationQueueEntry{if $queue->isNew()} interactiveDropdownItemOutstanding{/if}" data-link="{$queue->getLink()}" data-object-id="{@$queue->queueID}" data-is-read="{if $queue->isNew()}false{else}true{/if}">
		<div class="box32">
			<div class="framed">
				{@$queue->getUserProfile()->getAvatar()->getImageTag(32)}
			</div>
			<div>
				<h3><a href="{@$queue->getLink()}">{$queue->getAffectedObject()->getTitle()}</a></h3>
				<small>{if $queue->getUserProfile()->userID}<a href="{link controller='User' object=$queue->getUserProfile()->getDecoratedObject()}{/link}">{$queue->getAffectedObject()->getUsername()}</a>{else}{$queue->getAffectedObject()->getUsername()}{/if} - {@$queue->lastChangeTime|time}</small>
			</div>
		</div>
	</li>
{/foreach}