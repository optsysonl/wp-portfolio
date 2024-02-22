<?php

$rating = 5;
if (isset($_POST['rating'])) {
    $rating = (int)$_POST['rating'];
}

?>
<div class="rc-review-from">
    <form action="#" id="base-review-form">
        <input type="hidden" name="user-rating" value="<?php echo $rating ?>"/>
        <div class="rc-form-row">
            <input type="text" name="user-name" placeholder="Your name"/>
        </div>
        <div class="rc-form-row">
            <input type="text" name="user-email" placeholder="Your email"/>
        </div>
        <div class="rc-form-row">
            <textarea name="user-message" placeholder="Leave your review"></textarea>
        </div>
        <div class="rc-form-row">
            <button type="submit">Send</button>
        </div>
    </form>
</div>

