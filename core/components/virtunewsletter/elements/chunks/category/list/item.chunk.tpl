
<div class="checkbox">
    <label>
        <input
            type="checkbox"
            name="newsletter_categories[]"
            value="[[+virtuNewsletter.category.id]]"
            [[+virtuNewsletter.category.is_subscribed:is=`1`:then=`checked="checked"`]]
            > [[+virtuNewsletter.category.name]]
        <div><em>[[+virtuNewsletter.category.description]]</em></div>
    </label>
</div>
