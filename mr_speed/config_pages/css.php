<h3>Einstellungen für die CSS-Optimierung</h3>
<table class="form-table">
<tr>
    <th scope="row"><strong><?php echo $this->get_label('css'); ?></strong></th>
    <td><?php echo $this->get_input_field('css'); ?></td>
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
    <th scope="row"><?php echo $this->get_label('base64'); ?></th>
    <td><?php echo $this->get_input_field('base64'); ?></td>
</tr>
<tr>
    <th scope="row"><?php echo $this->get_label('base64_size'); ?></th>
    <td><?php echo $this->get_input_field('base64_size'); ?></td>
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
    <th colspan="2" scope="row"><?php echo $this->get_label('exclude'); ?></th>
</tr>
<tr>
    <td colspan="2"><?php echo $this->get_input_field('exclude'); ?></td>
</tr>
</table>
<p class="submit"><input type="submit" value="Einstellungen aktualisieren" class="button-primary" id="submit" name="submit"></p>