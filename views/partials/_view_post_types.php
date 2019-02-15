<div class="wpma-colum">
    <h4>Post Types</h4>
<select id="wpma-select-post-type" size="20">
<?php foreach (WPMA_Visual_Debugger::get_instance()->wpma_get_post_types() as $post_type) : ?>
  <option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
<?php endforeach; ?>
</select>
</div>
