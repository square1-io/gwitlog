<li class='gweet<?=$gweet->hasRemoteHost()?" linkable":"";?>' id="<?=$gweet->hash; ?>" data-linked="<?=$gweet->getRemoteLink(); ?>">
    <img src='<?=$gweet->getGravatar(75); ?>'
        height='75' width='75'
        alt='<?=htmlentities($gweet->username); ?>' class='avatar' />
    <div class='gweet-body'>
        <div class='gweet-meta'>
            <strong>
                <?=htmlentities($gweet->username); ?>
            </strong>
            <?php
            if (!empty($gweet->branch)) {
                ?>
                <span class='gweet-branch'>
                    pushed to
                    <?=$gweet->branch; ?>
                </span>
                <?php
            }
            ?>
            <span class="gweet-time">
                <time is="relative-time" datetime="<?=$gweet->getDateIso8601(); ?>">
                    <?=$gweet->date; ?>
                </time>
            </span>
        </div>
        <div class='gweet-detail'>
            <?=$gweet->message; ?>
        </div>
        <div class='gweet-commit'>
            <a href="<?=$gweet->getRemoteLink(); ?>">
                <?=$gweet->getShortHash(); ?>
            </a>
        </div>
    </div>
    <div class="clear"></div>
</li>
