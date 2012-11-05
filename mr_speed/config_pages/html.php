<h3>Einstellungen für die HTML-Optimierung</h3>
<table class="form-table">
<tr>
    <th scope="row"><strong><?php echo $this->get_label('html'); ?></strong></th>
    <td><?php echo $this->get_input_field('html'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('admin'); ?></th>
    <td><?php echo $this->get_input_field('admin'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('gzip'); ?></th>
    <td><?php echo $this->get_input_field('gzip'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('comments'); ?></th>
    <td><?php echo $this->get_input_field('comments'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('breaks'); ?></th>
    <td><?php echo $this->get_input_field('breaks'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('whitespaces'); ?></th>
    <td><?php echo $this->get_input_field('whitespaces'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('save_code'); ?></th>
    <td><?php echo $this->get_input_field('save_code'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('save_pre'); ?></th>
    <td><?php echo $this->get_input_field('save_pre'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('save_script'); ?></th>
    <td><?php echo $this->get_input_field('save_script'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('save_style'); ?></th>
    <td><?php echo $this->get_input_field('save_style'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('save_textarea'); ?></th>
    <td><?php echo $this->get_input_field('save_textarea'); ?></td>
</tr>
</table>
<p class="submit"><input type="submit" value="Einstellungen aktualisieren" class="button-primary" id="submit" name="submit"></p>