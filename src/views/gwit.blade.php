<li class='gwit @if($gwit->hasRemoteHost()){{ "linkable" }}@endif' id="{{ $gwit->hash }}"
    data-linked="{{ $gwit->getRemoteLink() }}">
    <img src='{{ $gwit->getGravatar(75) }}'
        height='75' width='75'
        alt='{{{ $gwit->username }}}' class='avatar' />
    <div class='gwit-body'>
        <div class='gwit-meta'>
            <strong>
                {{{ $gwit->username }}}
            </strong>
            @if (!empty($gwit->branch))
                <span class='gwit-branch'>
                    pushed to
                    {{{ $gwit->branch }}}
                </span>
            @endif
            <span class="gwit-time">
                <time is="relative-time" datetime="{{ $gwit->getDateIso8601() }}">
                    {{ $gwit->date }}
                </time>
            </span>
        </div>
        <div class='gwit-detail'>
            {{{ $gwit->message }}}
        </div>
        <div class='gwit-commit'>
            <a href="{{ $gwit->getRemoteLink() }}">
                {{ $gwit->getShortHash() }}
            </a>
        </div>
    </div>
    <div class="clear"></div>
</li>
