<div class="newsletter_footer">
        <?php echo tep_draw_form('index_newsletter', tep_href_link('boxnewsletter.php', '', 'SSL'), 'post', 'onsubmit="return validate();"', true) . tep_draw_hidden_field('action', 'process'); ?>
            <div class="clearfix">
                <div class="newsletter_footer_submit">
                    <button name="newsletter" title="Subscribe" type="submit"> </button>
                </div>
                <div class="newsletter_footer_input">
                    <input name="email_address" id="newsletter" class="input-text required-entry validate-email" type="text" value="Newsletter sign up"  onfocus="if(this.value=='Newsletter sign up'){this.value=''}" onblur="if(this.value==''){this.value='Newsletter sign up'}" />
                </div>
            </div>
        </form>
</div>